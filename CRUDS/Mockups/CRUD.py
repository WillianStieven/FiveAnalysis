import psycopg2

# Conexão com o banco
def conectar():
    return psycopg2.connect(
        dbname="Marketplace",  
        user="postgres",      
        password="123",     
        host="localhost",
        port="5432"
    )

# Função de login
def login():
    print("=============== Seja Bem-Vindo ===============")
    print("=== Para acessar o Menu, Faça Login abaixo ===\n")
    email = input("Email: ")
    senha = input("Senha: ")

    conn = conectar()
    cur = conn.cursor()
    cur.execute("SELECT id_cliente, nome FROM Cliente WHERE email = %s AND senha = %s", (email, senha))
    resultado = cur.fetchone()
    cur.close()
    conn.close()

    if resultado:
        print(f"\nBem-vindo, {resultado[1]}!")
        return resultado[0]  # retorna o id_cliente
    else:
        print("\nLogin inválido. Tente novamente.")
        return None

# 1. Listar todos os clientes
def listar_clientes():
    conn = conectar()
    cur = conn.cursor()
    cur.execute("SELECT * FROM Cliente")
    clientes = cur.fetchall()
    print("\n--- Lista de Clientes ---")
    for cliente in clientes:
        print(f"ID: {cliente[0]}, Nome: {cliente[1]}, Email: {cliente[2]}, CPF: {cliente[4]}")
    cur.close()
    conn.close()

# 2. Deletar Cliente
def deletar_cliente():
    id_cliente = input("Informe o ID do cliente que deseja deletar: ")
    conn = conectar()
    cur = conn.cursor()
    cur.execute("SELECT nome FROM Cliente WHERE id_cliente = %s", (id_cliente,))
    resultado = cur.fetchone()
    if resultado:
        cur.execute("DELETE FROM Cliente WHERE id_cliente = %s", (id_cliente,))
        conn.commit()
        print(f"Cliente '{resultado[0]}' deletado com sucesso.")
    else:
        print("Cliente não encontrado.")
    cur.close()
    conn.close()

# 3. Atualizar Cliente
def atualizar_cliente():
    id_cliente = input("Informe o ID do cliente que deseja atualizar: ")
    novo_nome = input("Novo nome: ")
    novo_email = input("Novo email: ")
    nova_senha = input("Nova senha: ")
    conn = conectar()
    cur = conn.cursor()
    cur.execute("UPDATE Cliente SET nome = %s, email = %s, senha = %s WHERE id_cliente = %s",
                (novo_nome, novo_email, nova_senha, id_cliente))
    if cur.rowcount > 0:
        conn.commit()
        print("Dados do cliente atualizados com sucesso.")
    else:
        print("Cliente não encontrado.")
    cur.close()
    conn.close()

# 4. Criar montagem de PC
def criar_montagem(id_cliente):
    conn = conectar()
    cur = conn.cursor()
    cur.execute("""
        INSERT INTO MontagemPC (id_cliente, data_criacao, status_compatibilidade, orcamento_estimado)
        VALUES (%s, CURRENT_DATE, TRUE, 0)
        RETURNING id_montagem
    """, (id_cliente,))
    id_montagem = cur.fetchone()[0]
    conn.commit()
    cur.close()
    conn.close()
    return id_montagem

# 5. Adicionar peça à montagem
def adicionar_peca_montagem(id_montagem, id_peca, quantidade):
    conn = conectar()
    cur = conn.cursor()
    cur.execute("""
        INSERT INTO Montagem_Pecas (id_montagem, id_peca, quantidade)
        VALUES (%s, %s, %s)
    """, (id_montagem, id_peca, quantidade))
    conn.commit()
    cur.close()
    conn.close()

# 6. Gerar reserva de peça
def gerar_reserva(id_cliente, id_peca):
    conn = conectar()
    cur = conn.cursor()
    cur.execute("""
        INSERT INTO ReservaCompra (id_cliente, id_peca, data, status)
        VALUES (%s, %s, CURRENT_DATE, 'Reservado')
    """, (id_cliente, id_peca))
    conn.commit()
    cur.close()
    conn.close()

# Menu interativo
def menu(id_cliente):
    while True:
        print("\n===== PAINEL DE CONTROLE =====")
        print("1 | Listar Clientes")
        print("2 | Deletar Cliente")
        print("3 | Atualizar Cliente")
        print("4 | Criar Montagem de PC")
        print("5 | Adicionar Peça à Montagem")
        print("6 | Reservar Peça")
        print("7 | Sair")
        print("================================")
        opcao = input("Escolha uma opção: ")

        if opcao == "1":
            listar_clientes()
        elif opcao == "2":
            deletar_cliente()
        elif opcao == "3":
            atualizar_cliente()
        elif opcao == "4":
            id_montagem = criar_montagem(id_cliente)
            print(f"Montagem criada com ID: {id_montagem}")
        elif opcao == "5":
            try:
                id_montagem = int(input("Digite o ID da montagem: "))
                id_peca = int(input("Digite o ID da peça: "))
                quantidade = int(input("Digite a quantidade: "))
                adicionar_peca_montagem(id_montagem, id_peca, quantidade)
                print("Peça adicionada à montagem com sucesso.")
            except ValueError:
                print("Entrada inválida. Certifique-se de digitar números inteiros.");
        elif opcao == "6":
            try:
                id_peca = int(input("Digite o ID da peça que deseja reservar: "))
                gerar_reserva(id_cliente, id_peca)
                print("Peça reservada com sucesso.")
            except ValueError:
                print("Entrada inválida. Certifique-se de digitar um número inteiro.")
        elif opcao == "7":
            print("Encerrando o programa...")
            break
        else:
            print("Opção inválida! Tente novamente.")

# Execução principal
def main():
    while True:
        id_cliente = login()
        if id_cliente:
            menu(id_cliente)
            break

# Iniciar o programa
main()
