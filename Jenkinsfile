pipeline {
    agent any

    environment {
        DOCKERHUB_USERNAME = "pyoawndi12"
        IMAGE_NAME = "php-data-aset-barang-80"
        IMAGE_TAG = "latest"
        IMAGE_FULL = "${DOCKERHUB_USERNAME}/${IMAGE_NAME}:${IMAGE_TAG}"
        CONTAINER_NAME = "php-data-aset-barang-80-container"
    }

    stages {

        stage('1. Build Docker Image') {
            steps {
                echo "Building image ${IMAGE_FULL}"
                sh """
                    docker build -t ${IMAGE_FULL} .
                """
            }
        }

        stage('2. Stop & Remove Old Container') {
            steps {
                sh """
                    docker stop ${CONTAINER_NAME} || true
                    docker rm ${CONTAINER_NAME} || true
                """
            }
        }

        stage('3. Login & Push Docker Hub') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'DockerHub-credencial',
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PWD'
                )]) {

                    sh '''
                        echo "$DOCKER_PWD" | docker login -u "$DOCKER_USER" --password-stdin
                    '''

                    sh """
                        docker push ${IMAGE_FULL}
                    """
                }
            }
        }

        stage('4. Run Container') {
            steps {
                sh """
                    docker run -d \
                        --name ${CONTAINER_NAME} \
                        --restart unless-stopped \
                        -p 8081:80 \
                        ${IMAGE_FULL}
                """
            }
        }
    }

    post {
        success {
            echo '✅ Deploy berhasil'
        }

        failure {
            echo '❌ Deploy gagal'
        }

        always {
            sh 'docker logout || true'
        }
    }
}