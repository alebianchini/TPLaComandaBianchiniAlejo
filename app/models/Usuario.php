<?php
require_once './db/AccesoDatosSql.php';
class Usuario
{
    public $id;
    public $mail;
    public $tipo;
    public $clave;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatosSql::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (mail, tipo, clave) VALUES (:mail, :tipo, :clave)");
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatosSql::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo, clave FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($mail, $tipo, $clave)
    {
        $objAccesoDatos = AccesoDatosSql::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo, clave FROM usuarios WHERE mail = :mail AND clave = :clave AND tipo = :tipo");
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function obtenerUsuarioPorId($id)
    {
        $objAccesoDatos = AccesoDatosSql::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario($usuario, $id)
    {
        $objAccesoDato = AccesoDatosSql::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET mail = :mail, tipo = :tipo, clave = :clave WHERE id = :id");
        $consulta->bindValue(':mail', $usuario->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $usuario->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $usuario->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function bajarUsuario($id)
    {
        $objAccesoDato = AccesoDatosSql::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("Y-m-d"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d'));
        $consulta->execute();
        return $consulta->rowCount();
    }

    public function consultarBajaUsuario()
    {
        $id = $this->id;
        $objAccesoDatos = AccesoDatosSql::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE id = :id AND fechaBaja != 'NULL'");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
}
