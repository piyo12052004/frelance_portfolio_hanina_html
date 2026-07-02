pipeline {
    agent any

    environment {
        DOCKER_USER = "username-dockerhub"
        IMAGE_NAME = "php-data-aset-barang-80"
        IMAGE_FULL = "${DOCKER_USER}/${IMAGE_NAME}:latest"
        CONTAINER_NAME = "php-data-aset-barang-80-container"
    }

    stages {

        stage('1. Build Docker Image') {
            steps {
                sh "docker build -t ${IMAGE_FULL} ."
            }
        }

        stage('2. Stop Container Lama') {
            steps {
                sh """
                docker stop ${CONTAINER_NAME} || true
                docker rm ${CONTAINER_NAME} || true
                """
            }
        }

        stage('3. Push Image ke Docker Hub') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'DockerHub-credencial',
                    passwordVariable: 'DOCKER_PWD',
                    usernameVariable: 'DOCKER_USER'
                )]) {

                    sh "echo $DOCKER_PWD | docker login -u $DOCKER_USER --password-stdin"
                    sh "docker push ${IMAGE_FULL}"
                }
            }
        }

        stage('4. Run Container Baru') {
            steps {
                sh """
                docker run -d \
                --name ${CONTAINER_NAME} \
                -p 8081:80 \
                ${IMAGE_FULL}
                """
            }
        }
    }
}