pipeline {
    agent any

    environment {
        IMAGE_NAME = "php-data-aset-barang-80"
        CONTAINER_NAME = "php-data-aset-barang-80-container"
    }

    stages {

        stage('Build Docker Image') {
            steps {
                sh "docker build -t ${IMAGE_NAME}:latest ."
            }
        }

        stage('Stop Container Lama') {
            steps {
                sh """
                docker stop ${CONTAINER_NAME} || true
                docker rm ${CONTAINER_NAME} || true
                """
            }
        }

        stage('Run Container Baru') {
            steps {
                sh """
                docker run -d \
                --name ${CONTAINER_NAME} \
                -p 8081:80 \
                ${IMAGE_NAME}:latest
                """
            }
        }
    }
}