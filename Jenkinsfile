pipeline {
  agent any
  stages {
    stage ('push artifact') {
      steps {
        dir ("/home/jonathan/Documents"){
          script {
            zip archive: true, dir: '', glob: '', zipFile: ''
          }
        }
      }
    }
  }
}
