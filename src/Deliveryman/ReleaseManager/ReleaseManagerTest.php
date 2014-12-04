<?php
namespace Deliveryman\ReleaseManager;

use Deliveryman\ReleaseManager\Filesystem\LocalFilesystem;
use Deliveryman\ReleaseManager\Filesystem\FilesystemInterface;

/**
 * Release manager test case
 * 
 * @author Alexander Sergeychik
 */
class ReleaseManagerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Tmp dir path
	 *
	 * @var string
	 */
	protected $tmpDir;
	
	/**
	 * Filesystem
	 * 
	 * @var FilesystemInterface
	 */
	protected $filesystem;
	
	/**
	 * Release manager
	 * 
	 * @var ReleaseManager
	 */
	protected $releaseManager;

	/**
	 * {@inheritDoc}
	 */
	public function setUp() {
		parent::setUp();
		
		$this->tmpDir = getcwd() . DIRECTORY_SEPARATOR . 'tmp';
		$this->rmdir($this->tmpDir);
		mkdir($this->tmpDir);
		
		$this->filesystem = new LocalFilesystem();
		$this->releaseManager = new ReleaseManager($this->tmpDir, $this->filesystem);
	
	}

	/**
	 * {@inheritDoc}
	 */
	protected function tearDown() {
		
		$this->releaseManager = null;
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
	 * Tests release manager paths
	 * 
	 * @return void
	 */
	public function testPaths() {
		
		$this->assertNotEmpty($this->releaseManager->getReleasesPath(), 'Releases path is not set');
		$this->assertNotSame($this->releaseManager->getBasePath(), $this->releaseManager->getReleasesPath(), 'Releases path targets to base path');
		
		$this->assertNotEmpty($this->releaseManager->getCurrentPath(), 'Current release symlink path is not set');
		$this->assertNotSame($this->releaseManager->getBasePath(), $this->releaseManager->getCurrentPath(), 'Current release symlink path targets to base path');
		
		$this->assertNotSame($this->releaseManager->getReleasesPath(), $this->releaseManager->getCurrentPath(), 'Releases path and symlink path are the same location');
	}
	
	/**
	 * Test release path
	 *
	 * @depends testPaths
	 */
	public function testReleasePath() {
	
		$releaseName = 'test-release';
		
		$expectedPath = $this->releaseManager->getReleasesPath() . $this->filesystem->getDirectorySeparator() . $releaseName;
		$actualPath = $this->releaseManager->getReleasePath($releaseName);
		
		$this->assertNotEmpty($actualPath, 'Release path is empty');
		$this->assertSame($expectedPath, $actualPath, sprintf('Release "%s" path does not match expected', $releaseName));
		
	}
	
	
	/**
	 * Tests release manager setup
	 * 
	 * @depends testPaths
	 * @return void
	 */
	public function testSetup() {
		$this->releaseManager->setup();	
		$this->assertTrue($this->filesystem->isDir($this->releaseManager->getReleasesPath()), 'Releases path is not created');		
	}
	
	/**
	 * Tests release creation
	 * 
	 * @depends testSetup
	 * @depends testReleasePath
	 * @return void
	 */
	public function testCreateRelease() {
			
		$this->releaseManager->setup();

		// test release creation with no params
		$releaseName = $this->releaseManager->createRelease();
		$this->assertNotEmpty($releaseName, 'Release name is empty');
		$this->assertTrue($this->filesystem->isDir($this->releaseManager->getReleasePath($releaseName)), 'Release directory is not created');

		// test specified release creation
		$releaseName = $this->releaseManager->createRelease('123123123');
		$this->assertNotEmpty($releaseName, 'Release name is empty');
		$this->assertTrue($this->filesystem->isDir($this->releaseManager->getReleasePath($releaseName)), 'Release directory is not created');
		
		// test duplicate release creation
		try {
			$this->releaseManager->createRelease($releaseName);
			$this->fail('Exception on duplicate release creation is not thrown');
		} catch (ReleaseManagerException $e) {
			$this->assertTrue(true);	
		}
		
	}
	
	/**
	 * Tests release presence test
	 * 
	 * @depends testSetup
	 * @depends testCreateRelease
	 * @return void
	 */
	public function testHasRelease() {
		
		$this->releaseManager->setup();
		
		$releaseName1 = $this->releaseManager->createRelease('release-1');
		$releaseName2 = $this->releaseManager->createRelease('release-2');
		$releaseName3 = 'release-4';
		
		$this->assertTrue($this->releaseManager->hasRelease($releaseName1), sprintf('Release "%s" is not recognized', $releaseName1));
		$this->assertTrue($this->releaseManager->hasRelease($releaseName2), sprintf('Release "%s" is not recognized', $releaseName2));
		$this->assertFalse($this->releaseManager->hasRelease($releaseName3), sprintf('Release "%s" is recognized, but does not exists', $releaseName3));
	}
	
	/**
	 * Tests release removal
	 * 
	 * @depends testSetup
	 * @depends testCreateRelease
	 * @depends testHasRelease
	 * @return void
	 */
	public function testRemoveRelease() {
		
		$this->releaseManager->setup();
		
		$releaseName = $this->releaseManager->createRelease();
		$this->assertNotEmpty($releaseName, 'Release name is empty');
		
		// remove existsing release
		$this->releaseManager->removeRelease($releaseName);
		$this->assertFalse($this->filesystem->isDir($this->releaseManager->getReleasePath($releaseName)), 'Release directory is not created');
		$this->assertFalse($this->releaseManager->hasRelease($releaseName));
		
		// remove not existing release
		try {
			$this->releaseManager->removeRelease('some-random-name-' . md5(rand(0,100)));
			$this->fail('Exception on non existsing release removal is not thrown');
		} catch (ReleaseManagerException $e) {
			$this->assertTrue(true);
		}
	}
	
	/**
	 * Tests release listing
	 * 
	 * @depends testSetup
	 * @depends testCreateRelease
	 * @depends testHasRelease
	 */
	public function testGetReleases() {
		
		$this->releaseManager->setup();
		
		$expectedReleases = array();
		for ($i = 1; $i < 10; $i++) {
			$expectedReleases[] = $this->releaseManager->createRelease('release-' . $i);
		}
		
		$releases = $this->releaseManager->getReleases();
		
		$this->assertNotEmpty($releases, 'Releases list is empty');
		$this->assertSameSize($expectedReleases, $releases, 'Releases list is not the same size');
		$this->assertSame($expectedReleases, $releases, 'Releases list is not equal to expected');
		
	}
	
	
	/**
	 * Tests setting release as current
	 * 
	 * @depends testSetup
	 * @depends testPaths
	 * @depends testCreateRelease
	 * @return void
	 */
	public function testSetCurrentRelease() {
		
		$this->releaseManager->setup();
		
		$release1 = $this->releaseManager->createRelease('release-1');
		$release2 = $this->releaseManager->createRelease('release-2');
		$release3 = 'release-3';
		
		// set first release as current
		$this->releaseManager->setCurrentRelease($release1);
		$this->assertTrue($this->filesystem->isSymlink($this->releaseManager->getCurrentPath()), 'Current path is not symlink');
		$this->assertEquals($this->releaseManager->getReleasePath($release1), $this->filesystem->readlink($this->releaseManager->getCurrentPath()), sprintf('Current path is not points to release "%s"', $release1));
		$this->assertNotEquals($this->releaseManager->getReleasePath($release2), $this->filesystem->readlink($this->releaseManager->getCurrentPath()), sprintf('Current path points to release "%s"', $release2));
		
		// set second release as current
		$this->releaseManager->setCurrentRelease($release2);
		$this->assertTrue($this->filesystem->isSymlink($this->releaseManager->getCurrentPath()), 'Current path is not symlink');
		$this->assertNotEquals($this->releaseManager->getReleasePath($release1), $this->filesystem->readlink($this->releaseManager->getCurrentPath()), sprintf('Current path points to release "%s"', $release1));
		$this->assertEquals($this->releaseManager->getReleasePath($release2), $this->filesystem->readlink($this->releaseManager->getCurrentPath()), sprintf('Current path is not points to release "%s"', $release2));
		
		
		// set third not existsin release as current
		try {
			$this->releaseManager->setCurrentRelease($release3);
			$this->fail('Exception on setting non existsing release as current is not thrown');
		} catch (ReleaseManagerException $e) {
			$this->assertTrue(true);
		}
		
	}
	
	/**
	 * Tests current release pointer
	 * 
	 * @depends testSetup
	 * @depends testPaths
	 * @depends testCreateRelease
	 */
	public function testGetCurrentRelease() {
		
		$this->releaseManager->setup();
		
		$release1 = $this->releaseManager->createRelease('release-1');
		$release2 = $this->releaseManager->createRelease('release-2');
		$release3 = 'release-3';
		
		// test if release not set
		$this->assertNull($this->releaseManager->getCurrentRelease(), 'When no current release not NULL value returned');		
		
		// test on first release
		$this->releaseManager->setCurrentRelease($release1);
		$this->assertSame($release1, $this->releaseManager->getCurrentRelease());
		$this->assertNotSame($release2, $this->releaseManager->getCurrentRelease());
		$this->assertNotSame($release3, $this->releaseManager->getCurrentRelease());
		
		// test on second release
		$this->releaseManager->setCurrentRelease($release2);
		$this->assertNotSame($release1, $this->releaseManager->getCurrentRelease());
		$this->assertSame($release2, $this->releaseManager->getCurrentRelease());
		$this->assertNotSame($release3, $this->releaseManager->getCurrentRelease());
		
		
	}
	
	
}
