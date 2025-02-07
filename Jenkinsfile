pipeline {
    agent any

    environment {
        IMAGE_NAME = "poc"
        CONTAINER_NAME = "poc-container"
        PORT = "8000"
        GITHUB_TOKEN = credentials('github-id') // Add your token in Jenkins credentials
    }

    stages {
        stage('SSH into Local Machine') {
            steps {
                sshagent(['poc-id']) {  // Use stored SSH credentials
                    sh "ssh -o StrictHostKeyChecking=no ttbsadmin@127.0.0.1 'echo Jenkins successfully connected to local machine!'"
                }
            }
        }

        stage('Clone Repository') {
            steps {
                script {

                    sh '''
                    
                    rm -rf * .git
                    git clone https://${GITHUB_TOKEN}:x-oauth-basic@github.com/frepinawk/poc.git .

                    '''
                }
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    sh "docker build -t ${IMAGE_NAME} ."
                }
            }
        }

        stage('Stop & Remove Existing Container') {
            steps {
                script {
                    sh "docker stop ${CONTAINER_NAME} || true"
                    sh "docker rm ${CONTAINER_NAME} || true"
                }
            }
        }

        stage('Run New Container') {
            steps {
                script {
                    sh "docker run -d -p ${PORT}:80 --name ${CONTAINER_NAME} ${IMAGE_NAME}"
                }
            }
        }
    }
}
