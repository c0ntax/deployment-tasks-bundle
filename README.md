# c0ntax/deployment-tasks-bundle

## Introduction

This is a Symfony bundle for the [c0ntax/deployment-tasks](https://github.com/c0ntax/deployment-tasks) library. It adds the ability
to run deployment tasks once (and only once) so that you don't need to manually run adhoc scripts as part of your build/deployment process.

## Installation

Run

```bash
composer req c0ntax/deployment-tasks-bundle
```

## Configuration

This package uses [knplabs/knp-gaufrette-bundle](https://github.com/KnpLabs/KnpGaufretteBundle) to store the tasks that have already been run, so a
little bit of configuration for that package is required in order to get this up and running.

Firstly, you need to configure your 'memory'. This will be where we store all the tasks that have been performed so that we do not perform them again.

In `knp_gaufrette.yaml`

```yaml
knp_gaufrette:
    adapters:
        tasks_local:
            service:
                id: c0ntax_deployment_tasks.gaufrette.adapter.local

        memory_local:
            local:
                directory: '%kernel.project_dir%/var/memory'
                
    filesystems:
        tasks:
            adapter: tasks_local
            alias: tasks_filesystem

        memory:
            adapter: memory_local
            alias: memory_filesystem
```

_NOTE:_ Currently (until I can figure out how to get a symfony bundle to configure another symfony bundle) there is a bit of
boilerplate that needs to be in the configuration. Just make sure that the `adapters.tasks_local` and `filesystems.tasks` is
copied in as-is from above.

This will set up a local disk memory store within the project. Obviously you can configure it anywhere. With Gaufrette, you can configure it
to [live almost anywhere](https://github.com/KnpLabs/KnpGaufretteBundle#configuring-the-adapters). So, you might want to store it in a database or and S3 bucket
in your production environments. Simply add a new adapter and then change the adapter configured for `memory`

By default, the place that is used to store the tasks is located in `%kernel.project_root/src/DeploymentTasks`. If you wish to change this, you simply need to override one parameter:

```yaml
parameters:
    c0ntax_deployment_tasks.directory.tasks: '%kernel.project_dir%/src/DeploymentTasks%'
```

## Usage

Running it is pretty simple. To run pre-deployment tasks (i.e. you have built your application but you have yet to put it live)

```bash
./bin/console deployment:tasks:run Pre
```

And to run post-deployment tasks (i.e. your application is now live to the world):

```bash
./bin/console deployment:tasks:run Post
```

Err, that's it
