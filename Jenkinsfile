pipeline {
  agent any
  stages {
    stage ('Create .zip from source code') {
      steps {
        deleteDir()
        script {
          zip archive: true, dir: '', glob: '', zipFile: 'ThalesMee-1.0.0.zip'
        }
      }
    }
    stage ('Deploy docker containers'){
      steps {
        sh "docker stop $(docker ps -aq)"
        sh "docker rm $(docker ps -aq)"
        sh "docker-compose up"
      }
    }
  }
}
