pipeline {
  agent any
  stages {
    stage ('Create .zip from source code') {
      steps {
        if (fileExists('ThalesMee-1.0.0.zip')) {
          sh 'rm ThalesMee-1.0.0.zip'
        }
        script {
          zip archive: true, dir: '', glob: '', zipFile: 'ThalesMee-1.0.0.zip'
        }
      }
    }
    stage ('Deploy docker containers'){
      steps {
        sh '''
          OLD = "$(docker ps -aq)"
          if [ -n "$OLD"]; then
            docker stop $OLD && docker rm $OLD
          fi
          docker-compose up
        '''
      }
    }
  }
}
