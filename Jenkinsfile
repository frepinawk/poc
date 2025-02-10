pipeline {
    agent any

    environment {
        IMAGE_NAME = "poc"
        CONTAINER_NAME = "poc-container"
        PORT = "8000"
        DOCKER_REPO = "frepino"
        GITHUB_TOKEN = credentials('github-id') // Add your token in Jenkins credentials
        VERSION = "v${BUILD_NUMBER}"
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
                    sh "git clone git@github.com:frepinawk/poc.git ."
                }
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    sh "docker build -t ${DOCKER_REPO}/${IMAGE_NAME}:${VERSION} ."
                }
            }
        }

        stage('Pushing The Image To Dockerhub'){
            steps {
                script {
                  withCredentials([string(credentialsId: 'dockerhub-access-token', variable: 'DOCKERHUB_TOKEN')]) {

                        sh "docker login -u ${DOCKER_REPO} -p ${DOCKERHUB_TOKEN}"
                        sh "docker tag ${DOCKER_REPO}/${IMAGE_NAME}:${VERSION} ${DOCKER_REPO}/${IMAGE_NAME}:latest"

                        sh "docker push ${DOCKER_REPO}/${IMAGE_NAME}:${VERSION}"
                        sh "docker push ${DOCKER_REPO}/${IMAGE_NAME}:latest"
                    }
                }
            }

        }

        stage('Pull Latest Image') {
            steps {
                script {
                        sh "docker pull ${DOCKER_REPO}/${IMAGE_NAME}:${VERSION}"
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
                    sh "docker run -d -p ${PORT}:80 --name ${CONTAINER_NAME} ${DOCKER_REPO}/${IMAGE_NAME}:${VERSION}"
                }
            }
        }
    }
}
