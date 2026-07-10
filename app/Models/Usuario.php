<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Usuario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Authenticate credentials and return user profile if valid.
     */
    public function autenticar(string $email, string $senha): ?array
    {
        $sql = "SELECT * FROM `usuarios` WHERE `email` = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            unset($user['senha']);
            return $user;
        }

        return null;
    }

    /**
     * Update email, name and password if provided.
     */
    public function atualizar(int $id, string $email, string $nome, ?string $novaSenha = null): bool
    {
        if (!empty($novaSenha)) {
            $sql = "UPDATE `usuarios` SET `email` = :email, `nome` = :nome, `senha` = :senha WHERE `id` = :id";
            $hashedPassword = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'email' => $email,
                'nome' => $nome,
                'senha' => $hashedPassword,
                'id' => $id
            ]);
        } else {
            $sql = "UPDATE `usuarios` SET `email` = :email, `nome` = :nome WHERE `id` = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'email' => $email,
                'nome' => $nome,
                'id' => $id
            ]);
        }
    }
}
