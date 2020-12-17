pipeline {
  agent any
  stages {
    stage ('push artifact') {
            steps {
                sh 'cd /home/jonathan'
                zip zipFile: 'test.zip', archive: false, dir: 'archive'
                archiveArtifacts artifacts: 'test.zip', fingerprint: true
            }
     }
  }
}
