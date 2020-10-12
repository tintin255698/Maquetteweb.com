<?php

namespace App\Form;

use App\Entity\Reservation;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [ 'attr'=>['placeholder'=>'Votre nom']])
            ->add('email', EmailType::class, [ 'attr'=>['placeholder'=>'Votre email ']])
            ->add('telephone', TelType::class, [ 'attr'=>['placeholder'=>'Votre téléphone']])
            ->add('heure', TimeType::class, array(
                'input'  => 'datetime',
                'widget' => 'choice',
                'hours' => array("12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17","18"=>"18","19"=>"19","20"=>"20","21"=>"21"),
                'empty_data' => null
            ))
            ->add('personne', IntegerType::class, [ 'attr'=>['placeholder'=>'Nombre de personnes']])
            ->add('message', TextareaType::class, [
                'required'=>false,
                'attr'=>['placeholder'=>'Message (facultatif)']]  )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
