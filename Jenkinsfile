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
        VERSION = "v${BUILD_NUMBER}"
    }

    stages {
        stage('Clean Workspace') {
            steps {
                script {
                    sh "rm -rf * .git || true"
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

        stage('Pushing The Image To Dockerhub') {
            steps {
                script {
                    withCredentials([string(credentialsId: 'dockerhub-access-token', variable: 'DOCKERHUB_TOKEN')]) {
                        sh """
                            echo "${DOCKERHUB_TOKEN}" | docker login -u "${DOCKER_REPO}" --password-stdin
                            docker tag ${DOCKER_REPO}/${IMAGE_NAME}:${VERSION} ${DOCKER_REPO}/${IMAGE_NAME}:latest
                            docker push ${DOCKER_REPO}/${IMAGE_NAME}:${VERSION}
                            docker push ${DOCKER_REPO}/${IMAGE_NAME}:latest
                        """
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

        stage('Run Docker Swarm Service') {
            steps {
                script {
                    sh """
                        # Initialize Docker Swarm (if not already initialized)
                        docker swarm init --advertise-addr 127.0.0.1 || true

                        # Remove existing service (if any)
                        docker service rm ${CONTAINER_NAME} || true

                        # Create new Docker Swarm service
                        docker service create --name ${CONTAINER_NAME} -p ${PORT}:80 ${DOCKER_REPO}/${IMAGE_NAME}:${VERSION}
                    """
                }
            }
        }
    }

    post {
        success {
            echo "Pipeline succeeded! Service is running on port ${PORT}."
        }
        failure {
            echo "Pipeline failed. Check logs for details."
        }
    }
}