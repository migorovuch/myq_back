pipeline {
  agent any

  stages {

    stage('Build environment') {
      steps {
        sh 'docker build -t myq_php --no-cache -f ./docker/php-fpm/prod/Dockerfile ./'
        sh 'docker build -t myq_nginx --no-cache -f ./docker/nginx/prod/Dockerfile ./'
        sh 'docker build -t myq_mysql --no-cache -f ./docker/mysql/prod/Dockerfile ./docker/mysql'
      }
    }

    stage('Run TEST environment') {
      steps {
        withCredentials([file(credentialsId: 'myq_test', variable: 'SECRETS')]) {
            writeFile file: './.env.test', text: readFile(SECRETS)
        }
        sh 'docker stop myq_mysql_test || true && docker stop myq_php_test || true &&  docker stop myq_nginx_test || true && docker network rm myq_network_test || true'
        sh 'docker network create myq_network_test'
        sh 'docker run --rm -t -d --network=myq_network_test --name myq_mysql_test --env-file .env.test myq_mysql'
//         sh 'until docker exec myq_mysql bash "mysqladmin ping"; do >&2 "MySQL is unavailable - sleeping"; sleep 2; done'
        sleep 30
        sh 'docker run --rm -t -d --network=myq_network_test --name myq_php_test --env-file .env.test myq_php php-fpm'
        sh 'docker run --rm -t -d --network=myq_network_test --name myq_nginx_test --env-file .env.test myq_nginx bash'
        sh 'docker exec myq_nginx_test bash -c \'echo "upstream php-upstream { server myq_php_test:9000; }" > /etc/nginx/conf.d/upstream.conf\''
        sh 'docker exec myq_nginx_test nginx'
      }
    }

    stage('Install TEST dependencies') {
        steps {
            sh 'docker exec myq_php_test composer install'
            sh 'docker exec myq_php_test bin/console lexik:jwt:generate-keypair || true'
        }
    }

    stage('Prepare TEST DB migrations&fixtures') {
      steps {
        sh 'docker exec myq_php_test bin/console doctrine:migrations:migrate'
        sh 'docker exec myq_php_test bin/console doctrine:fixtures:load -n'
      }
    }

    stage('Run PHP Unit tests') {
      steps {
        sh 'docker exec myq_php_test bin/phpunit --log-junit var/testResults/phpunit.xml --coverage-clover var/testResults/clover.xml'
        sh 'docker cp myq_php_test:/var/www/project/var/testResults/phpunit.xml ./testResults.xml'
//         sh 'docker cp myq_php:/var/www/project/var/testResults/clover.xml ./clover.xml'
        junit '**/testResults.xml'
      }
    }

//     stage('Code coverage') {
//       steps {
//         step([
//           $class: 'CloverPublisher',
//           cloverReportDir: '.',
//           cloverReportFileName: 'clover.xml',
//           healthyTarget: [methodCoverage: 80, conditionalCoverage: 80, statementCoverage: 80],
//           unhealthyTarget: [methodCoverage: 50, conditionalCoverage: 50, statementCoverage: 50],
//           failingTarget: [methodCoverage: 0, conditionalCoverage: 0, statementCoverage: 0]
//         ])
//       }
//     }

    stage('Stop TEST environment') {
      steps {
        sh 'docker stop myq_mysql_test'
        sh 'docker stop myq_php_test'
        sh 'docker stop myq_nginx_test'
        sh 'docker network rm myq_network_test'
      }
    }

    stage('Run environment') {
      steps {
        withCredentials([file(credentialsId: 'myq', variable: 'SECRETS')]) {
            writeFile file: './.env', text: readFile(SECRETS)
        }
        sh 'docker stop myq_mysql || true && docker stop myq_php || true &&  docker stop myq_nginx || true && docker network rm myq_network || true'
        sh 'docker network create myq_network'
        sh 'docker run --rm -t -d --network=myq_network --health-cmd="mysqladmin ping --silent" --health-interval=2s -p 3307:3306 --name myq_mysql --env-file .env myq_mysql'
//         sh 'until docker exec myq_mysql bash "mysqladmin ping"; do >&2 "MySQL is unavailable - sleeping"; sleep 2; done'
        sleep 30
        sh 'docker run --rm -t -d --network=myq_network --name myq_php --env-file .env myq_php php-fpm'
        sh 'docker run --rm -t -d --network=myq_network -p 80:80 --name myq_nginx --env-file .env myq_nginx'
      }
    }

    stage('Install dependencies') {
        steps {
            sh 'docker exec myq_php composer install'
            sh 'docker exec myq_php bin/console lexik:jwt:generate-keypair || true'
        }
    }

    stage('Run migrations') {
      steps {
        sh 'docker exec myq_php bin/console doctrine:migrations:migrate'
      }
    }
  }
}
