<?php
    abstract class Personnage
    {
        protected $atout,
            $degats,
            $id,
            $nom,
            $timeEndormi,
            $type;

        const CEST_MOI = 1; // Constante renvoyée par la méthode `frapper` si on se frappe soit-même.
        const PERSONNAGE_TUE = 2; // Constante renvoyée par la méthode `frapper` si on a tué le personnage en le frappant.
        const PERSONNAGE_FRAPPE = 3; // Constante renvoyée par la méthode `frapper` si on a bien frappé le personnage.
        const PERSONNAGE_ENSORCELE = 4; // Constante renvoyée par la méthode `lancerUnSort` (voir classe Magicien) si on a bien ensorcelé un personnage.
        const PAS_DE_MAGIE = 5; // Constante renvoyée par la méthode `lancerUnSort` (voir classe Magicien) si on veut jeter un sort alors que la magie du magicien est à 0.
        const PERSO_ENDORMI = 6; // Constante renvoyée par la méthode `frapper` si le personnage qui veut frapper est endormi.

        public function __construct(array $donnees)
        {
            $this->hydrate($donnees);
            $this->type = strtolower(static::class);
        }

        public function estEndormi()
        {
            return $this->timeEndormi > time();
        }

        public function frapper(Personnage $perso)
        {
            if ($perso->id == $this->id)
            {
                return self::CEST_MOI;
            }

            if ($this->estEndormi())
            {
                return self::PERSO_ENDORMI;
            }

            // On indique au personnage qu'il doit recevoir des dégâts.
            // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE.
            return $perso->recevoirDegats();
        }

        public function hydrate(array $donnees)
        {
            foreach ($donnees as $key => $value)
            {
                $method = 'set'.ucfirst($key);

                if (method_exists($this, $method))
                {
                    $this->$method($value);
                }
            }
        }

        public function nomValide()
        {
            return !empty($this->nom);
        }

        public function recevoirDegats()
        {
            $this->degats += 5;

            // Si on a 100 de dégâts ou plus, on supprime le personnage de la BDD.
            if ($this->degats >= 100)
            {
                return self::PERSONNAGE_TUE;
            }

            // Sinon, on se contente de mettre à jour les dégâts du personnage.
            return self::PERSONNAGE_FRAPPE;
        }

        public function reveil()
        {
            $secondes = $this->timeEndormi;
            $secondes -= time();

            $heures = floor($secondes / 3600);
            $secondes -= $heures * 3600;
            $minutes = floor($secondes / 60);
            $secondes -= $minutes * 60;

            $heures .= $heures <= 1 ? ' heure' : ' heures';
            $minutes .= $minutes <= 1 ? ' minute' : ' minutes';
            $secondes .= $secondes <= 1 ? ' seconde' : ' secondes';

            return $heures . ', ' . $minutes . ' et ' . $secondes;
        }

        public function atout()
        {
            return $this->atout;
        }

        public function degats()
        {
            return $this->degats;
        }

        public function id()
        {
            return $this->id;
        }

        public function nom()
        {
            return $this->nom;
        }

        public function timeEndormi()
        {
            return $this->timeEndormi;
        }

        public function type()
        {
            return $this->type;
        }

        public function setAtout($atout)
        {
            $atout = (int) $atout;

            if ($atout >= 0 && $atout <= 100)
            {
                $this->atout = $atout;
            }
        }

        public function setDegats($degats)
        {
            $degats = (int) $degats;

            if ($degats >= 0 && $degats <= 100)
            {
                $this->degats = $degats;
            }
        }

        public function setId($id)
        {
            $id = (int) $id;

            if ($id > 0)
            {
                $this->id = $id;
            }
        }

        public function setNom($nom)
        {
            if (is_string($nom))
            {
                $this->nom = $nom;
            }
        }

        public function setTimeEndormi($time)
        {
            $this->timeEndormi = (int) $time;
        }
    }

    class Magicien extends Personnage
    {
        protected $magie;
        public function lancerUnSort(Personnage $perso)
        {
            if ($this->degats >= 0 && $this->degats <= 25)
            {
                $this->atout = 4;
            }
            elseif ($this->degats > 25 && $this->degats <= 50)
            {
                $this->atout = 3;
            }
            elseif ($this->degats > 50 && $this->degats <= 75)
            {
                $this->atout = 2;
            }
            elseif ($this->degats > 75 && $this->degats <= 90)
            {
                $this->atout = 1;
            }
            else
            {
                $this->atout = 0;
            }

            if ($perso->id == $this->id)
            {
                return self::CEST_MOI;
            }

            if ($this->atout == 0)
            {
                return self::PAS_DE_MAGIE;
            }

            if ($this->estEndormi())
            {
                return self::PERSO_ENDORMI;
            }

            $perso->timeEndormi = time() + ($this->atout * 6) * 3600;

            return self::PERSONNAGE_ENSORCELE;
        }
    }

    $classeMagicien = new ReflectionClass('Magicien'); // Le nom de la classe doit être entre apostrophes ou guillemets.
    //$magicien = new Magicien(['nom' => 'vyk12', 'type' => 'magicien']);
    //$classeMagicien = new ReflectionObject($magicien);
    if ($classeMagicien->hasProperty('magie'))
    {
        echo 'La classe Magicien possède un attribut $magie';
    }
    else
    {
        echo 'La classe Magicien ne possède pas d\'attribut $magie';
    }

    if ($classeMagicien->hasMethod('lancerUnSort'))
    {
        echo 'La classe Magicien implémente une méthode lancerUnSort()';
    }
    else
    {
        echo 'La classe Magicien n\'implémente pas de méthode lancerUnSort()';
    }

    if ($classeMagicien->hasConstant('NOUVEAU'))
    {
        echo 'La classe Personnage possède une constante NOUVEAU';
    }
    else
    {
        echo 'La classe Personnage ne possède pas de constante NOUVEAU';
    }

    $classeMagicien = new ReflectionClass('Magicien');

    if ($parent = $classeMagicien->getParentClass())
    {
        echo 'La classe Magicien a un parent : il s\'agit de la classe ', $parent->getName();
    }
    else
    {
        echo 'La classe Magicien n\'a pas de parent';
    }

    if ($classeMagicien->isInterface())
    {
        echo 'La classe iMagicien est une interface';
    }
    else
    {
        echo 'La classe iMagicien n\'est pas une interface';
    }

    //$attributMagie = new ReflectionProperty('Magicien', 'magie');

    $classeMagicien = new ReflectionClass('Magicien');
    $attributMagie = $classeMagicien->getProperty('magie');
    var_dump($attributMagie);

    $classePersonnage = new ReflectionClass('Personnage');
    $attributsPersonnage = $classePersonnage->getProperties();
    var_dump($attributsPersonnage);

    $classeMagicien = new ReflectionClass('Magicien');
    $magicien = new Magicien(['nom' => 'vyk12', 'type' => 'magicien']);

    foreach ($classeMagicien->getProperties() as $attribut)
    {
        $attribut->setAccessible(true);
        echo $attribut->getName(), ' => ', $attribut->getValue($magicien);
    }

    $uneClasse = new ReflectionClass('Magicien');
echo '<br>';
    foreach ($uneClasse->getProperties() as $attribut)
    {
        echo $attribut->getName(), ' => attribut ';

        if ($attribut->isPublic())
        {
            echo 'public';
        }
        elseif ($attribut->isProtected())
        {
            echo 'protégé';
        }
        else
        {
            echo 'privé';
        }

        if ($attribut->isStatic())
        {
            echo ' (attribut statique)';
        }
    }
    class A
    {
        public function hello($arg1, $arg2, $arg3 = 1, $arg4 = 'Hello world !')
        {
            var_dump($arg1, $arg2, $arg3, $arg4);
        }
    }

    $a = new A;
    $hello = new ReflectionMethod('A', 'hello');

    $hello->invoke($a, 'test', 'autre test'); // On ne va passer que deux arguments à notre méthode.

    // A l'écran s'affichera donc :
    // string(4) "test" string(10) "autre test" int(1) string(13) "Hello world !"