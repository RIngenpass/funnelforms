<?php if (!isset($funnel) || !is_array($funnel)) return; ?>

<div class="funnel-container">
    <div class="funnel-progressbar"><div class="bar"></div></div>


    <?php foreach ($funnel as $stepIndex => $step): ?>
        <div class="funnel-step<?= $stepIndex === 0 ? ' active' : '' ?>"
             data-step="<?= $stepIndex ?>"
             data-next="<?= isset($step['next_step']) ? esc_attr($step['next_step']) : '' ?>"
             data-back="<?= isset($step['back_step']) ? esc_attr($step['back_step']) : '' ?>"
            <?= !empty($step['is_final']) ? 'data-final="true"' : '' ?>
            <?= !empty($step['on_complete']) ? 'data-on-complete="true"' : '' ?>>


        <h2><?= esc_html($step['title'] ?? 'Frage') ?></h2>


            <div class="funnel-elements">
                <?php foreach ($step['elements'] as $element): ?>
                    <div class="funnel-element">
                        <?php if (!empty($element['label'])): ?>
                            <label><?= esc_html($element['label']) ?></label>
                        <?php endif;

                        $name = esc_attr($element['name'] ?? uniqid('field_'));
                        $required = !empty($element['required']) ? 'required' : '';
                        $type = $element['type'];
                        ?>

                        <?php if (in_array($type, ['text', 'email', 'number'])): ?>
                            <input type="<?= $type ?>" name="<?= $name ?>" <?= $required ?>>

                        <?php elseif ($type === 'date'): ?>
                            <input type="text" name="<?= $name ?>" class="flatpickr" <?= $required ?>>

                        <?php elseif ($type === 'select'): ?>
                            <?php if (!empty($element['dynamic']) && !empty($element['shortcode'])): ?>
                                <div class="funnel-dynamic-select">
                                    <?= do_shortcode($element['shortcode']) ?>
                                </div>
                            <?php else: ?>
                                <select name="<?= $name ?>" <?= $required ?>>
                                    <?php foreach ($element['options'] as $option): ?>
                                        <option value="<?= esc_attr($option['label']) ?>">
                                            <?= esc_html($option['label']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>

                        <?php elseif ($type === 'textarea'): ?>
                            <textarea name="<?= $name ?>" class="funnel-textarea" <?= $required ?>></textarea>


                        <?php elseif ($type === 'contact7'): ?>
                            <div class="funnel-contact7">
                                <?= do_shortcode($element['shortcode'] ?? '') ?>
                            </div>

                        <?php elseif ($type === 'image-choice'): ?>
                            <div class="image-choice-wrapper-full">
                                <div class="image-choice-group" data-name="<?= esc_attr($element['name']) ?>">
                                    <?php foreach ($element['options'] as $option): ?>
                                        <label class="image-choice" data-next-step="<?= esc_attr($option['next']) ?>">
                                            <input type="radio" name="<?= esc_attr($element['name']) ?>" value="<?= esc_attr($option['label']) ?>" hidden>
                                            <img src="<?= esc_url($option['src']) ?>" alt="<?= esc_attr($option['label']) ?>">
                                            <div class="caption"><?= esc_html($option['label']) ?></div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php elseif ($type === 'info-text'): ?>
                            <div class="funnel-info-text">
                                <?= wp_kses_post($element['label']) ?>
                            </div>


                        <?php elseif ($type === 'video' && !empty($element['src'])): ?>
                            <div class="funnel-video">
                                <?php if (preg_match('/youtube\.com|youtu\.be/', $element['src'])): ?>
                                    <iframe width="100%" height="315" src="<?= esc_url($element['src']) ?>"
                                            frameborder="0" allowfullscreen></iframe>
                                <?php else: ?>
                                    <video width="100%" height="100%" controls>
                                        <source src="<?= esc_url($element['src']) ?>" type="video/mp4">
                                        Dein Browser unterstützt das Video-Tag nicht.
                                    </video>
                                <?php endif; ?>
                            </div>

                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="funnel-buttons">
        <button id="funnel-back" style="display:none">Zurück</button>
        <button id="funnel-next">Weiter</button>
    </div>
</div>

<script>
    flatpickr(".flatpickr", {
        enableTime: false,
        dateFormat: "Y-m-d"
    });


</script>
<script>
    const ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>

