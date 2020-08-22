<?php

namespace ValdeirPsr\PagSeguro\Domains\User;

class Factory
{
    /**
     * Cria a classe Sender
     * 
     * @param string $name
     * @param string $email
     * @param string $phone
     * @param Document $document
     * @param string $hash
     * 
     * @return Sender
     */
    public static function Sender(
        string $name = null,
        string $email = null,
        string $phone = null,
        Document $document = null,
        string $hash = null
    ): Sender {
        $sender = new Sender();

        if ($hash) {
            $sender->setHash($hash);
        }

        return self::fillData(
            $sender,
            $name,
            $email,
            $phone,
            $document
        );
    }

    /**
     * Cria a classe Holder
     * 
     * @param string $name
     * @param string $email
     * @param string $phone
     * @param Document $document
     * 
     * @return Holder
     */
    public static function Holder(
        string $name = null,
        string $email = null,
        string $phone = null,
        Document $document = null
    ): Holder {
        return self::fillData(
            new Holder(),
            $name,
            $email,
            $phone,
            $document
        );
    }

    /**
     * Preenche os dados da classe AbstractUser
     * 
     * @param AbstractUser $user
     * @param string $name
     * @param string $email
     * @param string $phone
     * @param Document $document
     * 
     * @return AbstractUser
     */
    private static function fillData(
        AbstractUser $user,
        string $name = null,
        string $email = null,
        string $phone = null,
        string $document = null
    ): Abstractuser {
        if ($name) {
            $user->setName($name);
        }

        if ($email) {
            $user->setEmail($email);
        }

        if ($phone) {
            $user->setPhone(
                substr($phone, 0, 2),
                substr($phone, 2),
            );
        }

        if ($document) {
            $user->setDocument($document);
        }

        return $user;
    }
}