pipeline {
    agent any

    environment {
        IMAGE_NAME = "poc"
        CONTAINER_NAME = "poc-container"
        PORT = "8000"
        GITHUB_TOKEN = credentials('github-id') // Add your token in Jenkins credentials
    }

    stages {
        stage('Clean Workspace') {  // Step to clean the workspace
            steps {
                script {
                    sh "rm -rf * .git || true"  // Remove existing files before cloning
                }
            }
        }

        stage('Clone Repository via SSH') {
            steps {
                sshagent(['github-ssh-key']) {  
                    sh "git clone git@github.com:your-username/your-repo.git ."
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
