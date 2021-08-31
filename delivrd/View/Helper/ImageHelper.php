<?php
/**
 * Image Helper class file.
 *
 * Simplifies the construction of Image elements.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Helper
 * @since         CakePHP(tm) v 0.9.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Html Helper class for easy use of HTML widgets.
 *
 * HtmlHelper encloses all methods needed while working with HTML pages.
 *
 * @package       Cake.View.Helper
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html
 */
App::uses('Helper', 'View');

class ImageHelper extends Helper {

    var $helpers = array('Html', 'Form');

    /**
     * Creates a formatted IMG element.
     *
     * This method will set an empty alt attribute if one is not supplied.
     *
     * ### Usage:
     *
     * Create a regular image:
     *
     * `echo $this->Html->image('cake_icon.png', array('alt' => 'CakePHP'));`
     *
     * Create an image link:
     *
     * `echo $this->Html->image('cake_icon.png', array('alt' => 'CakePHP', 'url' => 'http://cakephp.org'));`
     *
     * ### Options:
     *
     * - `url` If provided an image link will be generated and the link will point at
     *   `$options['url']`.
     * - `fullBase` If true the src attribute will get a full address for the image file.
     * - `plugin` False value will prevent parsing path as a plugin
     *
     * @param string $path Path to the image file, relative to the app/webroot/img/ directory.
     * @param array $options Array of HTML attributes. See above for special options.
     * @return string completed img tag
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::image
     */
    public function img($dir, $image, $type = 'thumb', $options = array(), $noimages = "no-image.gif", $tag = true) {
        if (file_exists('files' . "/" . $dir . "/" . $image) && is_file('files' . "/" . $dir . "/" . $image)) {
            $image = ($type) ? $type . '_' . $image : $image;
            $resized = Router::url('/', true) . 'files' . "/" . $dir . "/" . $image;
        } elseif(!empty($image)) {
            $resized = $image;
        } 
        else {
            $resized = Router::url('/', true) . 'img/' . $noimages;
        }
        if ($tag) {
            $resized = $this->Html->image($resized, $options);
        }
        return $resized;
    }

    public function resize($image = null, $width = 100, $height = 100, $options = array(), $noimages = "no_image_available.jpg", $ratio = true) {

        $thim_thumb = '&q=95';
        if ($width)
            $thim_thumb .= '&w=' . $width;

        if ($height && $ratio == false)
            $thim_thumb .= '&h=' . $height;

        if (file_exists("files/" . $image) && is_file("files/" . $image)) {
            $resized = $this->webroot . 'files/timthumb.php?src=' . FULL_BASE_URL . $this->webroot . APP_DIR . "/" . WEBROOT_DIR . "/" . 'files/' . $image . $thim_thumb;
        } else {
            $resized = $this->webroot . 'files/timthumb.php?src=' . FULL_BASE_URL . $this->webroot . APP_DIR . "/" . WEBROOT_DIR . "/" . 'img/' . $noimages . $thim_thumb;
        }
        return $resized;
    }

    public function renderField($options = array()) {
        ?>
        <div class="form-group last">
            <label class="control-label col-md-2"><?php echo (!empty($options['label'])) ? $options['label'] : 'Image'; ?></label>
            <div class="col-md-9">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="fileinput-new thumbnail" style="width: 200px;">
                        <?php
                        if (!empty($options['field']) && !empty($options['model']) && !empty($this->request->data[$options['model']][$options['field']]) && !empty($this->request->data[$options['model']]['id'])) {
                            $baseImagePath = (!empty($options['baseImagePath'])) ? $options['baseImagePath'] : Inflector::underscore($options['model']) . '/' . $options['field'];
                            echo $this->img($baseImagePath . '/' . $this->request->data[$options['model']]['id'], $this->request->data[$options['model']][$options['field']], '', array(), 'no-image.gif');
                        } else {
                            echo $this->img(strtolower($options['model']) . '/' . $options['field'] . '/', 100, 100, array(), 'no-image.gif');
                        }
                        $oldImage = (!empty($this->request->data[$options['model']][$options['field']]['name'])) ? @$this->request->data[$options['model']][$options['field']]['name'] : @$this->request->data[$options['model']][$options['field']];
                        echo $this->Form->hidden('old_'.$options['field'], array('value' => $oldImage));
                        ?>
                    </div>
                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                    <div>
                        <span class="btn default btn-file">
                            <span class="fileinput-new">
                                Select image </span>
                            <span class="fileinput-exists">
                                Change </span>
                            <?php echo $this->Form->file($options['model'] . '.' . $options['field'], array('type' => 'file', 'class' => 'm-wrap large', 'label' => false)); ?>
                        </span>

                        <a href="#" class="btn red fileinput-exists" data-dismiss="fileinput">
                            Remove </a>
                    </div>
                    <?php echo $this->Form->error($options['model'] . '.' . $options['field'], null, array('class' => 'help-block', 'wrap' => 'span')); ?>
                </div>

            </div>
        </div>
        <?php
    }

}
