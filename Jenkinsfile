pipeline {
    agent any
    stages {
        stage('Install Composer Dependencies') {
            steps {
                sh 'rm -rf composer.lock vendor/'
                sh 'composer install'
            }
        }

        stage('Install NPM Dependencies') {
            environment {
                PNPM_CACHE_FOLDER = "${env.WORKSPACE}/pnpm-cache/${env.BUILD_NUMBER}"
            }
            steps {
                sh 'n -d exec engine corepack enable pnpm'
                sh 'n -d exec engine pnpm install'
            }
        }

        stage('Check PHP Coding Style') {
            steps {
                sh 'composer run phpcs'
            }
        }

        stage('Check Formatting') {
            steps {
                sh 'n -d exec engine pnpm prettier'
            }
        }
    }
}
