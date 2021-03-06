<?php
/**
 * ContentType.php
 *
 * @since 23/08/14
 * @author Gerhard Seidel <gseidel.message@googlemail.com>
 */

namespace esperanto\ContentBundle\Form\Type;

use esperanto\ContentBundle\Item\ItemTypeResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class ContentType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var ItemTypeResolver
     */
    protected $resolver;

    public function __construct(ObjectManager $manager, ItemTypeResolver $resolver)
    {
        $this->resolver = $resolver;
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('items', 'collection', array(
            'type' => 'esperanto_content_item',
            'allow_delete' => true,
            'allow_add'    => true,
            'by_reference' => false
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $items = array();
        if(count($options['items'])) {
            foreach($options['items'] as $item) {
                $items[] = array(
                    'type' => $item['type'],
                    'label' => isset($item['label']) ? $item['label'] : $this->resolver->getLabel($item['type'])
                );
            }
        } else {
            foreach($this->resolver->getItems() as $name => $item) {
                $items[] = array(
                    'type' => $name,
                    'label' => $this->resolver->getLabel($name)
                );
            }
        }
        $view->vars['items'] = $items;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'esperanto\ContentBundle\Entity\Content',
            'items' => array()
        ));
    }

    public function getName()
    {
        return 'esperanto_content';
    }
}
