# 💰 Controle Financeiro Familiar

Este é um sistema web simples de **controle financeiro familiar**, desenvolvido em **PHP puro com MySQL**, com visual moderno utilizando **Bootstrap 5** e **Chart.js** para geração de gráficos.

---

## 🚀 Funcionalidades

- Cadastro de pessoas da casa e salários
- Cadastro de despesas com categoria e data
- Relatório financeiro com:
  - Total recebido (salários)
  - Total gasto (despesas)
  - Sobra (saldo do mês)
- Filtro por mês e ano
- Gráfico de gastos por categoria (Chart.js)
- Interface responsiva (Bootstrap 5)

---

## 🛠️ Tecnologias utilizadas

- PHP 
- MySQL
- HTML5 / CSS3
- Bootstrap 5
- Chart.js
- JavaScript (nativo)

---

## ⚙️ Requisitos

- PHP 7.4 ou superior
- Servidor web (ou usar o embutido do PHP)
- Banco de dados MySQL

---

## 💻 Instalação e execução

1. **Clone o repositório:**

```bash
git clone https://github.com/luskafonseca/controle.git
cd controle-familiar

2. Configure o banco de dados:

Crie um banco de dados MySQL com o nome controle_familiar

Importe o arquivo controle_familiar.sql  com as tabelas e dados iniciais

Edite o arquivo db.php com os dados da sua conexão

3. Inicie o servidor embutido do PHP:
php -S localhost:8000


Abra em seu navegador com http://localhost:8000