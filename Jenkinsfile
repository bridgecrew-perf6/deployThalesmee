pipeline {
  agent any

  stages {
    stage ('Delete old file') {
      steps {
        script {
          if (fileExists("ThalesMee-1.0.0.zip")) {
            sh 'rm ThalesMee-1.0.0.zip'
          }
        }
      }
    }
    stage ('Create .zip from source code') {
      steps {
        script {
          zip archive: true, dir: '', glob: '', zipFile: 'ThalesMee-1.0.0.zip'
        }
      }
    }
    stage ('Deploy docker containers'){
      steps {
        sh '''
          nb = $(docker ps -aq) | wc -l
          if [ $nb -ne 0]; then
            docker stop $(docker ps -aq) && docker rm $(docker ps -aq)
          fi
          docker-compose up -d
        '''
      }
    }
  }
}
