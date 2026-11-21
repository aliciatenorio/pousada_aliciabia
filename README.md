Sistema de Gerenciamento de Pousada

Integrantes
- Alicia Tenório Rodriguez
- Bianca Negrão Moreira
  
Tema do projeto
O projeto consiste em um sistema web para gerenciamento de uma pousada. O sistema permite cadastrar, consultar, editar e excluir informações de hóspedes, funcionários, quartos e reservas. Os tipos de quarto são utilizados para caracterizar os quartos cadastrados. O sistema também apresenta relatórios relacionados às reservas.

Tecnologias utilizadas
- PHP
- MySQL
- HTML e CSS
- PDO para conexão e operações com o banco de dados
  
Tabelas do banco de dados
O banco de dados utilizado é hotel_db e possui as seguintes tabelas:
1. tbHospedes – armazena os dados dos hóspedes.
2. tbFuncionarios – armazena os dados dos funcionários.
3. tbTipos_Quarto – armazena os tipos de quarto, suas descrições, valores de diária e capacidades.
4. tbQuartos – armazena os quartos, seus números, andares, situações e tipos.
5. tbReservas – armazena as reservas, relacionando hóspedes e funcionários às datas e aos valores.
6. tbReservas_Quartos – relaciona as reservas aos quartos.
   
As tabelas utilizam chaves primárias e chaves estrangeiras para estabelecer os relacionamentos. As restrições ON DELETE RESTRICT impedem a exclusão de registros que ainda possuem vínculos com outras tabelas.

Extras implementados
- Ordenação dos registros pelas colunas das listagens, em ordem crescente ou decrescente.
- Tela inicial com a quantidade de registros de hóspedes, funcionários, tipos de quarto e reservas cadastrados.
- Relatório de quantidade de reservas por hóspede.
- Relatório de quantidade de reservas por funcionário.
- Tela de confirmação antes de realizar exclusões.
- Validação dos dados dos formulários, incluindo validação das datas das reservas.
- Mensagens de retorno após cadastro, edição e exclusão.
  
Como instalar e executar
1. Instale e abra o XAMPP.
2. Inicie os serviços Apache e MySQL.
3. Copie a pasta do projeto para o diretório htdocs do XAMPP.
4. Acesse o phpMyAdmin pelo endereço http://localhost/phpmyadmin.
5. Importe o arquivo banco.sql, que está na pasta principal do projeto. Esse arquivo cria o banco hotel_db, suas tabelas e alguns registros de exemplo.
6. Abra o arquivo conexao.php e confira as configurações de conexão com o MySQL:
   - servidor: 127.0.0.1;
   - banco de dados: hotel_db;
   - usuário: informe o usuário configurado no seu MySQL (no XAMPP, geralmente é root);
   - senha: informe a senha configurada para esse usuário. Se não houver senha, deixe o valor vazio ('').
7. Salve as alterações e acesse o projeto pelo navegador, utilizando http://localhost/pousada_aliciabia-main/ (o endereço deve corresponder ao nome da pasta dentro de htdocs).
   
Uso de inteligência artificial e fontes de consulta
Durante o desenvolvimento, a dupla utilizou o W3Schools para consultar conteúdos e esclarecer dúvidas sobre PHP, HTML, CSS e banco de dados.

A inteligência artificial foi utilizada como apoio para pedir explicações sobre o funcionamento de alguns recursos extras e ajudar a compreender como poderiam ser implementados. As explicações foram usadas como material de estudo para que a dupla entendesse os recursos e pudesse trabalhar no projeto.
