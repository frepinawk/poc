pipeline {
    agent any

    environment {
        IMAGE_NAME = "POC"
        CONTAINER_NAME = "POC-container"
        PORT = "8000"
        GITHUB_TOKEN = credentials('github-id') // Add your token in Jenkins credentials
    }

    stages {
        stage('Clone Repository') {
            steps {
                script {
                    
                    sh "git clone https://${GITHUB_TOKEN}:x-oauth-basic@github.com/frepinawk/poc.git ."
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
