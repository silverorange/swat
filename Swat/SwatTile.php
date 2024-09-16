<?php

/**
 * A tile in a {@link SwatTileView}
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
 * @lisence   http://www.gnu.org/copyleft/lesser.html LGPL Lisence 2.1
 * @see       SwatTileView
 */
class SwatTile extends SwatCellRendererContainer
{


    /**
     * Whether or not to include CSS classes from the first cell renderer
     * of this tile in this tile's CSS classes
     *
     * @see SwatTile::getCSSClassNames()
     */
    public $show_renderer_classes = true;



    /**
     * Messages affixed to this tile
     *
     * @var array
     */
    protected $messages = [];



    /**
     * Displays this tile using a data object
     *
     * @param mixed $data a data object used to display the cell renderers in
     *                     this tile.
     */
    public function display($data)
    {
        if (!$this->visible) {
            return;
        }

        $this->setupRenderers($data);
        $this->displayRenderers($data);
    }



    /**
     * Initializes this tile
     *
     * This initializes the tile contained in the tile view
     */
    public function init()
    {
        foreach ($this->renderers as $renderer) {
            $renderer->init();
        }
    }



    /**
     * Processes this tile
     *
     * Processes each renderer contained in the tile
     */
    public function process()
    {
        foreach ($this->renderers as $renderer) {
            $renderer->process();
        }
    }



    /**
     * Gathers all messages from this tile
     *
     * @return array an array of {@link SwatMessage} objects.
     */
    public function getMessages()
    {
        $messages = $this->messages;

        foreach ($this->renderers as $renderer) {
            $messages = array_merge($messages, $renderer->getMessages());
        }

        return $messages;
    }



    /**
     * Adds a message to this tile
     *
     * @param SwatMessage the message to add.
     *
     * @see SwatMessage
     */
    public function addMessage(SwatMessage $message)
    {
        $this->messages[] = $message;
    }



    /**
     * Gets whether or not this tile has any messages
     *
     * @return boolean true if this tile has one or more messages and
     *                 false if it does not.
     */
    public function hasMessage()
    {
        $has_message = false;

        foreach ($this->renderers as $renderer) {
            if ($renderer->hasMessage()) {
                $has_message = true;
                break;
            }
        }

        return $has_message;
    }



    /**
     * Sets properties of renderers using data from current row
     *
     * @param mixed $data the data object being used to render the cell
     *                     renderers of this field.
     */
    protected function setupRenderers($data)
    {
        if (count($this->renderers) === 0) {
            throw new SwatException(
                'No renderer has been provided for this tile.',
            );
        }

        $sensitive = $this->parent->isSensitive();

        // Set the properties of the renderers to the value of the data field.
        foreach ($this->renderers as $renderer) {
            $this->renderers->applyMappingsToRenderer($renderer, $data);
            $renderer->sensitive = $renderer->sensitive && $sensitive;
        }
    }



    /**
     * Renders cell renderers
     *
     * @param mixed $data the data object being used to render the cell
     *                     renderers of this field.
     */
    protected function displayRenderers($data)
    {
        $div_tag = new SwatHtmlTag('div');
        $div_tag->class = $this->getCSSClassString();
        $div_tag->open();
        $this->displayRenderersInternal($data);
        $div_tag->close();
    }



    /**
     * Renders each cell renderer in this tile
     *
     * If there is one cell renderer in this tile, it is rendered by itself.
     * If there is more than one cell renderer in this tile, cell renderers
     * are rendered in order inside separate <i>div</i> elements. There is no
     * separation between multiple cell renderers within a single tile.
     *
     * @param mixed $data the data object being used to render the cell
     *                     renderers of this field.
     */
    protected function displayRenderersInternal($data)
    {
        if (count($this->renderers) === 1) {
            $this->renderers->getFirst()->render();
        } else {
            $div_tag = new SwatHtmlTag('div');
            foreach ($this->renderers as $renderer) {
                // get renderer class names
                $classes = ['swat-tile-view-tile-renderer'];
                $classes = array_merge(
                    $classes,
                    $renderer->getInheritanceCSSClassNames(),
                );

                $classes = array_merge(
                    $classes,
                    $renderer->getBaseCSSClassNames(),
                );

                $classes = array_merge(
                    $classes,
                    $renderer->getDataSpecificCSSClassNames(),
                );

                $classes = array_merge($classes, $renderer->classes);

                $div_tag->class = implode(' ', $classes);
                $div_tag->open();
                $renderer->render();
                $div_tag->close();
            }
        }
    }



    /**
     * Gets the array of CSS classes that are applied to this tile
     *
     * CSS classes are added to this tile in the following order:
     *
     * 1. hard-coded CSS classes from tile subclasses,
     * 2. user-specified CSS classes on this tile,
     *
     * If {@link SwatTile::$show_renderer_classes} is true, the following
     * extra CSS classes are added:
     *
     * 3. the inheritance classes of the first cell renderer in this tile,
     * 4. hard-coded CSS classes from the first cell renderer in this tile,
     * 5. hard-coded data-specific CSS classes from the first cell renderer in
     *    this tile if this tile has data mappings applied,
     * 6. user-specified CSS classes on the first cell renderer in this tile.
     *
     * @return array the array of CSS classes that are applied to this tile.
     *
     * @see SwatCellRenderer::getInheritanceCSSClassNames()
     * @see SwatCellRenderer::getBaseCSSClassNames()
     * @see SwatUIObject::getCSSClassNames()
     */
    protected function getCSSClassNames()
    {
        // base classes
        $classes = $this->getBaseCSSClassNames();

        // user-specified classes
        $classes = array_merge($classes, $this->classes);

        $first_renderer = $this->renderers->getFirst();
        if (
            $this->show_renderer_classes &&
            $first_renderer instanceof SwatCellRenderer
        ) {
            // renderer inheritance classes
            $classes = array_merge(
                $classes,
                $first_renderer->getInheritanceCSSClassNames(),
            );

            // renderer base classes
            $classes = array_merge(
                $classes,
                $first_renderer->getBaseCSSClassNames(),
            );

            // renderer data specific classes
            if ($this->renderers->mappingsApplied()) {
                $classes = array_merge(
                    $classes,
                    $first_renderer->getDataSpecificCSSClassNames(),
                );
            }

            // renderer user-specified classes
            $classes = array_merge($classes, $first_renderer->classes);
        }

        return $classes;
    }



    /**
     * Gets the base CSS class names of this tile
     *
     * This is the recommended place for column subclasses to add extra hard-
     * coded CSS classes.
     *
     * @return array the array of base CSS class names for this tile.
     */
    protected function getBaseCSSClassNames()
    {
        return ['swat-tile'];
    }

}
