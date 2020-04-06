<?php
    require('MonExeption.php');
    function additionner($a, $b)
    {
        if (!is_numeric($a) || !is_numeric($b))
        {
            // On lance une nouvelle exception grâce à throw et on instancie directement un objet de la classe Exception.
            throw new MonException('Les deux paramètres doivent être des nombres');
        }

        if (func_num_args() > 2)
        {
            throw new Exception('Pas plus de deux arguments ne doivent être passés à la fonction'); // Cette fois-ci, on lance une exception "Exception".
        }

        return $a + $b;
    }

    try // On va essayer d'effectuer les instructions situées dans ce bloc.
    {
        echo additionner(12, 3), '<br />';
        //echo additionner('azerty', 54), '<br />';
        echo additionner(15, 54, 45), '<br />';
        echo additionner(4, 8);
    }

    catch (MonException $e) // On va attraper les exceptions "Exception" s'il y en a une qui est levée.
    {
        echo $e;
    }

    catch (Exception $e) // Si l'exception n'est toujours pas attrapée, alors nous allons essayer d'attraper l'exception "Exception".
    {
        echo '[Exception] : ', $e->getMessage(); // La méthode __toString() nous affiche trop d'informations, nous voulons juste le message d'erreur.
    }
