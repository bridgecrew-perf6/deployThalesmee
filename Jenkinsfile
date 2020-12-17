pipeline {
  agent any
  stages {
    stage ('push artifact') {
            steps {
                sh "cd /home/jonathan"
              script {
                zip zipFile : "/home/jonathan", archive: true, dir: '', glob: '', zipFile: 'nameOfFile'
              }
            }
     }
  }
}
