pipeline {
  agent any
  stages {
    stage ('push artifact') {
      steps {
        dir ("/home/jonathan"){
          script {
            zip archive: true, dir: '', glob: '', zipFile: '/home/jonathan'
          }
        }
      }
    }
  }
}
