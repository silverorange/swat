pipeline {
    agent any
    stages {
        stage('Install Composer Dependencies') {
            steps {
                sh 'rm -rf composer.lock vendor/'
                sh 'composer install'
            }
        }

        stage('Install Yarn Dependencies') {
            environment {
                YARN_CACHE_FOLDER = "${env.WORKSPACE}/yarn-cache/${env.BUILD_NUMBER}"
            }
            steps {
                sh 'yarn install'
            }
        }

        stage('Lint Modified Files') {
            when {
                not {
                    branch 'master'
                }
            }
            steps {
                sh '''
                    master_sha=$(git rev-parse origin/master)
                    newest_sha=$(git rev-parse HEAD)
                    composer run phpcs
                    $(git diff --diff-filter=ACRM --name-only $master_sha...$newest_sha)
                '''
            }
        }

        stage('Lint Entire Project') {
            when {
                branch 'master'
            }
            steps {
                sh 'composer run phpcs'
            }
        }

        stage('Check if Pretty') {
            steps {
                sh 'yarn run prettier'
            }
        }
    }
}
