pipeline {
  agent any
  stages {
    stage ('push artifact') {
      dir ("/home/jonathan"){
        steps {
            sh "cd /home/jonathan"
          script {
            zip archive: true, dir: '', glob: '', zipFile: '/home/jonathan'
          }
        }
      }
    }
  }
}
