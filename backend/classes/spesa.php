<?php
class Spesa
{
    protected string $email_utente;
    protected mixed $importo;
    protected string $descrizione;
    protected string $categoria;

    public function __construct(string $email_utente, mixed $importo, string $descrizione, string $categoria)
    {
        $this->email_utente = $this->setEmail_utente($email_utente);
        $this->importo = $this->setImporto($importo);
        $this->descrizione = $this->setDescrizione($descrizione);
        $this->categoria = $this->setCategoria($categoria);
    }

    private function setEmail_utente(string $email_utente): string
    {
        if (isset($email_utente) && filter_var($email_utente, FILTER_VALIDATE_EMAIL)) {
            return $email_utente;
        } else {
            throw new Exception("Email non valida!");
        }
    }

    private function setImporto(mixed $importo): float
    {
        $importo = str_replace(',', '.', trim((string) $importo));

        if (is_numeric($importo)) {
            return (float) $importo;
        } else {
            throw new Exception("Importo non valido!");
        }
    }

    private function setCategoria(string $categoria): string
    {
        if ($categoria == "casa" || $categoria == "cibo" || $categoria == "extra" || $categoria == "studio" || $categoria == "svago" || $categoria == "trasporti") {
            return $categoria;
        } else {
            throw new Exception("Categoria non valida!");
        }
    }

    private function setDescrizione(string $descrizione): string
    {
        return $descrizione;
    }

    public function getImporto(): float
    {
        return $this->importo;
    }
}
?>