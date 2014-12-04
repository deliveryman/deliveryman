<?php
namespace Deliveryman\ReleaseManager\Filesystem;

/**
 * Tests local filesystem class
 *
 * @author Alexander Sergeychik
 */
class LocalFilesystemTest extends \PHPUnit_Framework_TestCase {

	/**
	 *
	 * @var string
	 */
	protected $tmpDir;

	/**
	 *
	 * @var FilesystemInterface
	 */
	protected $filesystem;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp() {
		parent::setUp();
		
		$this->tmpDir = getcwd() . DIRECTORY_SEPARATOR . 'tmp';
		$this->rmdir($this->tmpDir);
		mkdir($this->tmpDir);
		
		$this->filesystem = new LocalFilesystem();
	}

	/**
	 * {@inheritDoc}
	 */
	protected function tearDown() {
		
		$this->filesystem = null;
		
		$this->rmdir($this->tmpDir);
		$this->tmpDir = null;
		
		parent::tearDown();
	}

	/**
	 * Removes directory recursively
	 *
	 * @param string $path        	
	 * @return void
	 */
	protected function rmdir($path) {
		if (!$path || !is_dir($path)) return;
		
		$dir = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($iterator as $file) {
			if ($file->isLink()) {
				unlink($file->getPathname());
			} elseif ($file->isDir()) {
				rmdir($file->getPathname());
			} else {
				unlink($file->getPathname());
			}
		}
		rmdir($path);
		return;
	}

	/**
	 * Makes directory with specific contents
	 *
	 * @param string $path        	
	 * @param array $contents        	
	 */
	protected function mkdir($path, array $contents = array()) {
		if (is_dir($path)) return;
		mkdir($path);
		
		foreach ($contents as $key => $value) {
			if (is_array($value)) {
				$this->mkdir($path . DIRECTORY_SEPARATOR . $key, $value);
			} elseif (is_string($key)) {
				file_put_contents($path . DIRECTORY_SEPARATOR . $key, $value);
			} elseif (is_numeric($key) && is_string($value)) {
				touch($path . DIRECTORY_SEPARATOR . $value);
			}
		}
		
		return;
	}

	/**
	 * Tests LocalFilesystem::getDirectorySeparator()
	 *
	 * @return void
	 */
	public function testGetDirectorySeparator() {
		
		$separator = $this->filesystem->getDirectorySeparator();
		
		$this->assertNotEmpty($separator, 'Separator is empty');
		$this->assertEquals(DIRECTORY_SEPARATOR, $separator, sprintf('Separator "%s" is not match local system directory separator "%s"', $separator, DIRECTORY_SEPARATOR));
	}

	/**
	 * Tests LocalFilesystem::isAbsolute()
	 *
	 * @return void
	 */
	public function testIsAbsolute() {
		
		$relativePath = basename($this->tmpDir);
		$absolutePath = $this->tmpDir;
		
		$this->assertFalse($this->filesystem->isAbsolute($relativePath), sprintf('Relative path "%s" detected as absolute', $relativePath));
		$this->assertTrue($this->filesystem->isAbsolute($absolutePath), sprintf('Absulute path "%s" detected as relative', $absolutePath));
	}

	/**
	 * Tests LocalFilesystem::isDir()
	 *
	 * @return void
	 */
	public function testIsDir() {
		
		$filePath = $this->tmpDir . DIRECTORY_SEPARATOR . 'file.txt';
		$dirPath = $this->tmpDir . DIRECTORY_SEPARATOR . 'tmp_dir';
		$fileSymlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'symlink_file';
		$dirSymlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'symlink_dir';
		
		touch($filePath);
		mkdir($dirPath);
		symlink($filePath, $fileSymlink);
		symlink($dirPath, $dirSymlink);
		
		$this->assertFalse($this->filesystem->isDir($filePath), 'File is detected as directory');
		$this->assertTrue($this->filesystem->isDir($dirPath), 'Directory is not detected as directory');
		$this->assertFalse($this->filesystem->isDir($fileSymlink), 'Symlink to file is detected as directory');
		$this->assertFalse($this->filesystem->isDir($dirSymlink), 'Symlink to directory is detected as directory');
	
	}

	/**
	 * Tests LocalFilesystem::isFile()
	 *
	 * @return void
	 */
	public function testIsFile() {
		
		$filePath = $this->tmpDir . DIRECTORY_SEPARATOR . 'file.txt';
		$dirPath = $this->tmpDir . DIRECTORY_SEPARATOR . 'tmp_dir';
		$fileSymlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'symlink_file';
		$dirSymlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'symlink_dir';
		
		touch($filePath);
		mkdir($dirPath);
		symlink($filePath, $fileSymlink);
		symlink($dirPath, $dirSymlink);
		
		$this->assertTrue($this->filesystem->isFile($filePath), 'File is not detected as file');
		$this->assertFalse($this->filesystem->isFile($dirPath), 'Directory is detected as file');
		$this->assertFalse($this->filesystem->isFile($fileSymlink), 'Symlink to file is detected as file');
		$this->assertFalse($this->filesystem->isFile($dirSymlink), 'Symlink to directory is detected as file');
	
	}

	/**
	 * Tests LocalFilesystem::isSymlink()
	 *
	 * @return void
	 */
	public function testIsSymlink() {
		
		$filePath = $this->tmpDir . DIRECTORY_SEPARATOR . 'file.txt';
		$dirPath = $this->tmpDir . DIRECTORY_SEPARATOR . 'tmp_dir';
		$fileSymlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'symlink_file';
		$dirSymlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'symlink_dir';
		
		touch($filePath);
		mkdir($dirPath);
		symlink($filePath, $fileSymlink);
		symlink($dirPath, $dirSymlink);
		
		$this->assertFalse($this->filesystem->isSymlink($filePath), 'File is detected as symlink');
		$this->assertFalse($this->filesystem->isSymlink($dirPath), 'Directory is detected as symlink');
		$this->assertTrue($this->filesystem->isSymlink($fileSymlink), 'Symlink to file is not detected as symlink');
		$this->assertTrue($this->filesystem->isSymlink($dirSymlink), 'Symlink to directory is not detected as symlink');
	
	}

	/**
	 * Tests LocalFilesystem::ls()
	 *
	 * @return void
	 */
	public function testLs() {
		
		$dir = $this->tmpDir . DIRECTORY_SEPARATOR . 'test';
		$file = $this->tmpDir . DIRECTORY_SEPARATOR . 'test.txt';
		$fake = $this->tmpDir . DIRECTORY_SEPARATOR . 'test2';
		
		$structure = array(
			'dir1' => array(
				'testfile1.txt', 
				'testfile2.txt'
			), 
			'dir2' => array(
				'testfile1.txt', 
				'testfile2.txt'
			), 
			'testfile1.txt' => '123', 
			'testfile2.txt' => '1234'
		);
		$this->mkdir($dir, $structure);
		touch($file);
		
		// test list
		$list = $this->filesystem->ls($dir);
		
		$this->assertTrue(is_array($list), sprintf('Returned value is not array but "%s"', gettype($list)));
		$this->assertSameSize($structure, $list, 'Item count is not match to generated structure');
		$this->assertSame(array_keys($structure), array_values($list), 'Item names is not equal');
		
		$paths = array();
		foreach (array_keys($structure) as $subpath) {
			$paths[] = $dir . DIRECTORY_SEPARATOR . $subpath;
		}
		$this->assertSame($paths, array_keys($list), 'Paths to files is not equal');
		
		// test list of file
		try {
			$this->filesystem->ls($file);
			$this->fail('No exception thrown when ls() on file');
		} catch (FilesystemException $e) {
			$this->assertTrue(true);
		} catch (\Exception $e) {
			$this->fail(sprintf('Wrong exception type "%s" with message: %s', get_class($e), $e->getMessage()));
		}
		
		// test list of unknown
		try {
			$this->filesystem->ls($fake);
			$this->fail('No exception thrown when ls() on fake location');
		} catch (FilesystemException $e) {
			$this->assertTrue(true);
		} catch (\Exception $e) {
			$this->fail(sprintf('Wrong exception type "%s" with message: %s', get_class($e), $e->getMessage()));
		}
	
	}

	/**
	 * Tests LocalFilesystem::mkdir(..., false)
	 *
	 * @return void
	 */
	public function testMkdirWithoutParents() {
		$dir = $this->tmpDir . DIRECTORY_SEPARATOR . 'test';
		$file = $this->tmpDir . DIRECTORY_SEPARATOR . 'file.txt';
		touch($file);
		
		// test if directory not exists
		$this->filesystem->mkdir($dir, false);
		$this->assertTrue(is_dir($dir), sprintf('Directory "%s" not created', $dir));
		
		// try to create directory that exists
		try {
			$this->filesystem->mkdir($dir, false);
			$this->fail('No exception thrown when mkdir() on existing location');
		} catch (FilesystemException $e) {
			$this->assertTrue(true);
		}
		
		// try to create directory if file with the same name exists
		try {
			$this->filesystem->mkdir($file, false);
			$this->fail('No exception thrown when mkdir() on existing file location');
		} catch (FilesystemException $e) {
			$this->assertTrue(true);
		}
	
	}

	/**
	 * Tests LocalFilesystem::mkdir(..., true)
	 *
	 * @return void
	 */
	public function testMkdirWithParents() {
		$dir = $this->tmpDir . DIRECTORY_SEPARATOR . 'first' . DIRECTORY_SEPARATOR . 'second' . DIRECTORY_SEPARATOR . 'third';
		
		// test if directory not exists
		$this->filesystem->mkdir($dir, true);
		$this->assertTrue(is_dir($dir), sprintf('Directory "%s" not created', $dir));
		
		// try to create directory that exists
		try {
			$this->filesystem->mkdir($dir, true);
			$this->fail('No exception thrown when mkdir() on existing location');
		} catch (FilesystemException $e) {
			$this->assertTrue(true);
		}
	
	}

	/**
	 * Tests LocalFilesystem::rm()
	 *
	 * @return void
	 */
	public function testRm() {
		
		$dir = $this->tmpDir . DIRECTORY_SEPARATOR . 'test';
		$structure = array(
			'notempty' => array(
				'testfile1.txt', 
				'testfile2.txt'
			), 
			'notempty_subdir' => array(
				'subdir' => array(
					'testfile3.txt'
				), 
				'testfile1.txt', 
				'testfile2.txt'
			), 
			'empty' => array(), 
			'testfile1.txt' => '123', 
			'testfile2.txt' => '1234'
		);
		$this->mkdir($dir, $structure);
		$symlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'test_symlink';
		symlink($dir . DIRECTORY_SEPARATOR . 'testfile1.txt', $symlink);
		
		// remove file
		$this->filesystem->rm($dir . DIRECTORY_SEPARATOR . 'testfile1.txt');
		$this->assertFileNotExists($dir . DIRECTORY_SEPARATOR . 'testfile1.txt', 'File is not deleted');
		
		// remove empty dir with no recursive
		$this->filesystem->rm($dir . DIRECTORY_SEPARATOR . 'empty');
		$this->assertFileNotExists($dir . DIRECTORY_SEPARATOR . 'empty', 'Empty directory is not deleted with no recursive flag');
		
		// remove not empty dir with no recursive
		try {
			$this->filesystem->rm($dir . DIRECTORY_SEPARATOR . 'notempty', false);
			$this->fail('No exception thrown when rm(..., false) on not empty directory');
		} catch (FilesystemException $e) {
			$this->assertTrue(true);
		}
		
		// remove not empty dir with recursive
		try {
			$this->filesystem->rm($dir . DIRECTORY_SEPARATOR . 'notempty', true);
			$this->assertFileNotExists($dir . DIRECTORY_SEPARATOR . 'empty', 'Not empty directory is not deleted with recursive flag');
		} catch (FilesystemException $e) {
			$this->fail('Exception thrown when recursive rm(..., true) on not empty directory');
		}
		
		// remove not empty dir with subdirectories using recursive
		try {
			$this->filesystem->rm($dir . DIRECTORY_SEPARATOR . 'notempty_subdir', true);
			$this->assertFileNotExists($dir . DIRECTORY_SEPARATOR . 'empty', 'Not empty directory with subdirs is not deleted with recursive flag');
		} catch (FilesystemException $e) {
			$this->fail('Exception thrown when recursive rm(..., true) on not empty directory with subdirs');
		}
		
		// symlink removal
		$this->filesystem->rm($symlink);
		$this->assertFileNotExists($symlink, 'Symlink is not deleted by rm()');
		
	}
	
	/**
	 * Tests LocalFilesystem::symlink()
	 * 
	 * @depends testIsSymlink
	 * @return void
	 */
	public function testSymlink() {
		
		$dir = $this->tmpDir . DIRECTORY_SEPARATOR . 'dir';
		$file = $this->tmpDir . DIRECTORY_SEPARATOR . 'file.txt';
		
		$fileSymlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'file_symlink';
		$dirSymlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'dir_symlink';
		$symlinkSymlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'symlink_symlink';
		
		mkdir($dir);
		touch($file);
		
		$this->filesystem->symlink($file, $fileSymlink);
		$this->assertFileExists($fileSymlink, 'File symlink is not created');
		$this->assertTrue($this->filesystem->isSymlink($fileSymlink), 'Created file symlink is not actually symlink');
		
		$this->filesystem->symlink($dir, $dirSymlink);
		$this->assertFileExists($dirSymlink, 'Dir symlink is not created');
		$this->assertTrue($this->filesystem->isSymlink($dirSymlink), 'Created dir symlink is not actually symlink');
		
		$this->filesystem->symlink($fileSymlink, $symlinkSymlink);
		$this->assertFileExists($dirSymlink, 'Symlink to symlink is not created');
		$this->assertTrue($this->filesystem->isSymlink($dirSymlink), 'Created symlink to symlink is not actually symlink');
		
		
		// test overwrite
		$overwriteSymlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'overwrite_symlink';
		
		$this->filesystem->symlink($file, $overwriteSymlink, false);
		$this->filesystem->symlink($dir, $overwriteSymlink, true);
		
	}
	
	
	/**
	 * Tests LocalFilesystem::unlink()
	 *
	 * @depends testIsSymlink
	 * @return void
	 */
	public function testUnlink() {
		
		$dir = $this->tmpDir . DIRECTORY_SEPARATOR . 'dir';
		$file = $this->tmpDir . DIRECTORY_SEPARATOR . 'file.txt';
		$dirSymlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'dir_symlink';
		$fileSymlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'file_symlink';
		
		mkdir($dir);
		touch($file);
		symlink($file, $fileSymlink);
		symlink($dir, $dirSymlink);
		
		// unlink symlink to file removes symlink, not file
		$this->filesystem->unlink($fileSymlink);
		$this->assertFileNotExists($fileSymlink, 'Symlink to file is not deleted');
		$this->assertFileExists($file, 'Symlink target file is deleted');
		
		// unlink symlink to dir removes symlink, not dir
		$this->filesystem->unlink($dirSymlink);
		$this->assertFileNotExists($dirSymlink, 'Symlink to dir is not deleted');
		$this->assertFileExists($dir, 'Symlink target dir is deleted');
		
		// unlink file removes file
		$this->filesystem->unlink($file);
		$this->assertFileNotExists($file, 'File is not deleted');
		
		// unlink dir throws exception
		try {
			$this->filesystem->unlink($dir);
			$this->fail('Exception on directory is not thrown');
		} catch (FilesystemException $e) {
			$this->assertFileExists($dir, 'Symlink target dir is deleted');
		}
		
	}

	
	/**
	 * Tests LocalFilesystem::readlink()
	 *
	 * @depends testSymlink
	 * @return void
	 */
	public function testReadlink() {
	
		
		$file = $this->tmpDir . DIRECTORY_SEPARATOR . 'file.txt';
		$symlink = $this->tmpDir . DIRECTORY_SEPARATOR . 'symlink';
		$symlinkFake = $this->tmpDir . DIRECTORY_SEPARATOR . 'symlink_fake';
		
		touch($file);
		symlink($file, $symlink);
		
		// reading existing symlink
		$this->assertNotEmpty($this->filesystem->readlink($symlink), 'Symlink target is empty!');
		$this->assertSame($file, $this->filesystem->readlink($symlink), 'Symlink target is not recognized');
		
		// reading fake symlink
		try {
			$this->filesystem->readlink($symlinkFake);
			$this->fail('Exception on not existsing symlink is not thrown');
		} catch (FilesystemException $e) {
			$this->assertTrue(true);
		}
		
	}
	
}
