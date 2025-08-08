# Use a imagem base do PHP
FROM php:8.4-fpm

# Definição de variáveis
ARG user=projetos
ARG uid=1000

# Cria usuário do sistema para rodar comandos do Composer e Artisan
RUN useradd -m -u $uid -G www-data,root -s /bin/bash $user && mkdir -p /home/$user/.composer && chown -R $user:$user /home/$user

# Atualiza e instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    nano \
    procps\
    lsof\
    net-tools\
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copia arquivos de configuração do PHP
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/start.sh /usr/local/bin/start.sh

# Instala Node.js e npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get update && apt-get install -y nodejs && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensões do PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

# Instala e configura o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala Redis
RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto
COPY . /var/www/html

# Configura permissões do diretório do projeto
RUN chown -R $user:$user /var/www/html

# Permite que o usuário tenha permissões de root temporariamente para instalação
USER root

# Instala dependências do PHP e Node.js antes de mudar para o usuário
RUN git config --global --add safe.directory /var/www/html && composer install --no-interaction --no-progress || true && chown -R $user:$user /var/www/html

RUN npm install

# Retorna para o usuário especificado
USER $user

# Expõe a porta 8080 (PHP-FPM) e a porta 5173 (Vite)
EXPOSE 8080 5173

# Define o entrypoint para o script de inicialização
ENTRYPOINT ["/usr/local/bin/start.sh"]
