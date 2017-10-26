#!groovy
pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                sh "tar -cjf shinage-server.tar.bz2 *"
            }
        }
        stage('Deploy') {
            steps {
                sh "cp shinage-server.tar.bz2 /opt/shinage-devel/"

                sh "cd /opt/shinage-devel/"
                sh "tar -xjf shinage-server.tar.bz2"

                sh "composer install"
                sh "chown www-data:www-data . -R"
            }
        }
    }
}