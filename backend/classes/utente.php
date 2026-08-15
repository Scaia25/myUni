<?php
class Utente
{
    protected string $email;
    protected string $nome;
    protected string $cognome;
    protected string $password;

    public function __construct(string $email, string $nome, string $cognome, string $password)
    {
        $this->email = $this->setEmail($email);
        $this->nome = $this->setNome($nome);
        $this->cognome = $this->setCognome($cognome);
        $this->password = $this->setPassword($password);
    }

    private function setEmail(string $email): string
    {
        if (isset($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        } else {
            throw new Exception("Email non valida!");
        }
    }

    private function setNome(string $nome): string
    {
        if (isset($nome)) {
            return $nome;
        } else {
            throw new Exception("Nome non valido!");
        }
    }

    private function setCognome(string $cognome): string
    {
        if (isset($cognome)) {
            return $cognome;
        } else {
            throw new Exception("Cognome non valido!");
        }
    }

    private function setPassword(string $password): string
    {
        if (isset($password)) {
            return password_hash($password, PASSWORD_DEFAULT);
        } else {
            throw new Exception("Password non valida!");
        }
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getNome(): string {
        return $this->nome;
    }

    public function getCognome(): string {
        return $this->cognome;
    }

    public function getPassword(): string {
        return $this->password;
    }
}
?>