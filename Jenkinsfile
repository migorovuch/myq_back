pipeline {
  agent any

  stages {

    stage('Build environment') {
      steps {
        sh 'docker build --target stage -t myq_php -f ./docker/php-fpm/Dockerfile ./'
        sh 'docker build -t myq_nginx -f ./docker/nginx/Dockerfile ./'
        sh 'docker build -t myq_mysql -f ./docker/mysql/Dockerfile ./docker/mysql'
      }
    }

    stage('Run TEST environment') {
      steps {
        withCredentials([file(credentialsId: 'myq_test', variable: 'SECRETS')]) {
            writeFile file: './.env.test', text: readFile(SECRETS)
        }
        sh 'docker stop myq_mysql_test || true && docker stop php_test || true &&  docker stop myq_nginx_test || true && docker network rm myq_network_test || true'
        sh 'docker network create myq_network_test'
        sh 'docker run -t -d --network=myq_network_test --name myq_mysql_test --env-file .env.test myq_mysql'
//         sh 'until docker exec myq_mysql bash "mysqladmin ping"; do >&2 "MySQL is unavailable - sleeping"; sleep 2; done'
        sleep 30
        sh 'docker run -t -d --network=myq_network_test --name php_test -e XDEBUG_CONFIG="start_with_request=no mode=coverage" --env-file .env.test myq_php php-fpm'
        sh 'docker run -t -d --network=myq_network_test --name myq_nginx_test --env-file .env.test myq_nginx bash'
        sh 'docker exec myq_nginx_test bash -c \'echo "upstream php-upstream { server php_test:9000; }" > /etc/nginx/conf.d/upstream.conf\''
        sh 'docker exec myq_nginx_test nginx'
      }
    }

    stage('Install TEST dependencies') {
        steps {
            sh 'docker exec php_test composer install'
            sh 'docker exec php_test bin/console lexik:jwt:generate-keypair || true'
        }
    }

    stage('Prepare TEST DB migrations&fixtures') {
      steps {
        sh 'docker exec php_test bin/console doctrine:migrations:migrate'
        sh 'docker exec php_test bin/console doctrine:fixtures:load -n'
      }
    }

    stage('Run PHP Unit tests') {
      steps {
        sh 'docker exec php_test bin/phpunit --log-junit var/testResults/phpunit.xml --coverage-clover var/testResults/clover.xml'
        sh 'docker cp php_test:/var/www/html/var/testResults/phpunit.xml ${env.WORKSPACE}/testResults.xml'
        sh 'docker cp php_test:/var/www/html/var/testResults/clover.xml ${env.WORKSPACE}/clover.xml'
        junit '**/testResults.xml'
      }
    }

    stage('Code coverage') {
      steps {
        step([
          $class: 'CloverPublisher',
          cloverReportDir: "${env.WORKSPACE}",
          cloverReportFileName: 'clover.xml',
          healthyTarget: [methodCoverage: 80, conditionalCoverage: 80, statementCoverage: 80],
          unhealthyTarget: [methodCoverage: 50, conditionalCoverage: 50, statementCoverage: 50],
          failingTarget: [methodCoverage: 0, conditionalCoverage: 0, statementCoverage: 0]
        ])
      }
    }

    stage('Stop TEST environment') {
      steps {
        sh 'docker stop myq_mysql_test || true'
        sh 'docker rm -f myq_mysql_test || true'
        sh 'docker stop php_test || true'
        sh 'docker rm -f php_test || true'
        sh 'docker stop myq_nginx_test || true'
        sh 'docker rm -f myq_nginx_test || true'
        sh 'docker network rm myq_network_test'
      }
    }

    stage('Run environment') {
      steps {
        withCredentials([file(credentialsId: 'myq', variable: 'SECRETS')]) {
            writeFile file: './.env.local', text: readFile(SECRETS)
        }
        sh 'docker stop myq_mysql || true && docker stop php || true &&  docker stop myq_nginx || true && docker network rm myq_network || true'
        sh 'docker rm myq_mysql || true && docker rm php || true &&  docker rm myq_nginx || true'
        sh 'docker network create myq_network'
        withCredentials([string(credentialsId: 'volumes_path', variable: 'VOLUMES_PATH')]) {
            sh '''
                set +x
                docker run -t -d --network=myq_network -v $VOLUMES_PATH/mysql:/var/lib/mysql -p 3307:3306 --name myq_mysql --env-file .env.local myq_mysql
            '''
//         sh 'until docker exec myq_mysql bash "mysqladmin ping"; do >&2 "MySQL is unavailable - sleeping"; sleep 2; done'
            sleep 30
            sh 'docker run -t -d --network=myq_network -v $VOLUMES_PATH/media:/var/www/html/public/media --name php --env-file .env.local myq_php php-fpm'
            sh 'docker run -t -d --network=myq_network -v $VOLUMES_PATH/media:/var/www/html/public/media -p 80:80 --name myq_nginx --env-file .env.local myq_nginx'
        }
      }
    }

    stage('Install dependencies') {
        steps {
            sh 'docker exec php composer install'
            sh 'docker exec php bin/console assets:install --symlink || true'
            sh 'docker exec php bin/console lexik:jwt:generate-keypair || true'
            sh 'docker exec php chown -R www-data:www-data /var/www/html/var'

            sh 'mkdir -p ./public_bundles_nelmioapidoc'
            sh 'docker cp php:/var/www/html/vendor/nelmio/api-doc-bundle/Resources/public/. ./public_bundles_nelmioapidoc/'
            sh 'docker exec myq_nginx mkdir -p /var/www/html/public/bundles'
            sh 'docker cp ./public_bundles_nelmioapidoc/. myq_nginx:/var/www/html/public/bundles/nelmioapidoc/'
        }
    }

    stage('Run migrations') {
      steps {
        sh 'docker exec php bin/console doctrine:migrations:migrate'
      }
    }

    stage('Copy FRONT') {
      steps {
        sh 'docker cp myq_node:/app/dist/. ./public/front/'
        sh 'docker cp ./public/front/. myq_nginx:/var/www/html/public/front/'
      }
    }
  }
}
