pipeline {
    agent any

    parameters {
        string(name: 'BRANCH_NAME', defaultValue: 'main', description: 'Git branch to build')
    }

    environment {
        IMAGE_NAME = "poc"
        CONTAINER_NAME = "poc-container"
        PORT = "8000"
        DOCKER_REPO = "frepino"
       // GITHUB_TOKEN = credentials('github-id') // If using https for git cloning , git clone https://${GITHUB_TOKEN}@github.com/frepinawk/poc.git .
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
                    sh "git clone -b ${params.BRANCH_NAME} git@github.com:frepinawk/poc.git ."
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

                        sh '''
                    echo "${DOCKERHUB_TOKEN}" | docker login -u "${DOCKER_REPO}" --password-stdin
                    docker tag ${DOCKER_REPO}/${IMAGE_NAME}:${VERSION} ${DOCKER_REPO}/${IMAGE_NAME}:latest
                    docker push ${DOCKER_REPO}/${IMAGE_NAME}:${VERSION}
                    docker push ${DOCKER_REPO}/${IMAGE_NAME}:latest
                '''
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
