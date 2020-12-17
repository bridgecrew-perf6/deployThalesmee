pipeline {
  agent any
  stages {
    stage ('push artifact') {
            steps {
                sh "cd /home/jonathan"
                zip archive: true, dir: '', glob: '', zipFile: 'nameOfFile'
            }
     }
  }
}
