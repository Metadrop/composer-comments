<?php

namespace Metadrop\Composer\Comments;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Installer\PackageEvents;
use Composer\Installer\PackageEvent;
use Composer\Plugin\Capable;



use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\Package\PackageInterface;
use InvalidArgumentException;


/**
 * Class that implements the Composer Comments Plugin.
 */
class CommentsPlugin implements PluginInterface, EventSubscriberInterface, Capable {


  /**
   * @var Composer Composer instance.
   */
  private $composer;

  /**
   *  @var IOInterface Object to handle IO operations.
   */
  private $io;


  /**
   * {@inheritdoc}
   */
  public function activate(Composer $composer, IOInterface $io): void {
    $this->composer = $composer;
    $this->io = $io;
  }


  /**
   * {@inheritdoc}
   */
  public function getCapabilities(): array {
    return [
      'Composer\Plugin\Capability\CommandProvider' => 'Metadrop\Composer\Comments\CommandProvider',
    ];
  }


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      PackageEvents::POST_PACKAGE_UNINSTALL => 'displayComments',
      PackageEvents::POST_PACKAGE_INSTALL => 'displayComments',
      PackageEvents::POST_PACKAGE_UPDATE => 'displayComments'
    ];
  }

  /**
   * Callback for events on packages.
   *
   * Main entry point to display comments on each package, regardless of the
   * operation used.
   *
   * @param PackageEvent $event Event that triggerd the callback.
   */
  public function displayComments(PackageEvent $event) {

    $commentsManager = new CommentsManager($this->composer);
    $commentsManager->intialize();

    $package = $this->getPackageFromOperation($event->getOperation());
    $name = $package->getName();

    if ($commentsManager->hasComment($name)) {
      $this->io->write(sprintf('    Comment found for package <info>%s</info>: <warning>%s</warning>', $name, $commentsManager->getComment($name)));
    }
  }


  /**
   * Get a Package object from an OperationInterface object.
   *
   * Different operations have different ways to get the package that are
   * handling. This function abstracts that difference.
   *
   * @param OperationInterface $operation Operation that is running.
   *
   * @return PackageInterface An instance that behaves like a package.
   *
   * @throws InvalidArgumentException In case the operation is unknown.
   */
  protected function getPackageFromOperation(OperationInterface $operation): PackageInterface {
    if ($operation instanceof InstallOperation || $operation instanceof UninstallOperation) {
      $package = $operation->getPackage();
    } elseif ($operation instanceof UpdateOperation) {
      $package = $operation->getTargetPackage();
    } else {
      throw new InvalidArgumentException('Unknown operation: ' . get_class($operation));
    }
    return $package;
  }

  /**
   * {@inheritdoc}
   */
  public function deactivate(Composer $composer, IOInterface $io) {
  }

  /**
   * {@inheritdoc}
   */
  public function uninstall(Composer $composer, IOInterface $io) {
  }
}
