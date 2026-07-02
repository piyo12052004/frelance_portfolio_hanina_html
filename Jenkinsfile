pipeline {

    agent any

    environment {
        DOCKERHUB_USERNAME = "pyoawndi12"
        IMAGE_NAME = "php-data-aset-barang-80"
        IMAGE_TAG = "latest"
        IMAGE_FULL = "${DOCKERHUB_USERNAME}/${IMAGE_NAME}:${IMAGE_TAG}"
    }

    stages {

        stage('Build Docker Image') {
            steps {
                sh "docker build -t ${IMAGE_FULL} ."
            }
        }

        stage('Login Docker Hub') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'DockerHub-credencial',
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PWD'
                )]) {

                    sh '''
                        echo "$DOCKER_PWD" | docker login \
                        -u "$DOCKER_USER" \
                        --password-stdin
                    '''
                }
            }
        }

        stage('Push Docker Image') {
            steps {
                sh "docker push ${IMAGE_FULL}"
            }
        }

        stage('Deploy') {
            steps {
                sh '''
                    docker compose pull app

                    docker compose up -d \
                        --no-deps \
                        --force-recreate \
                        app

                    docker image prune -f
                '''
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