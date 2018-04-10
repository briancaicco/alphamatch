<?php

$days = Inventor_Post_Types::opening_hours_day_names();

$time_format = get_option('time_format');

// make sure we specify each part of the value we need.
$defaults = array_fill_keys( array_keys( $days ), array() );
$value = wp_parse_args( $value, $defaults );
?>

<table>
    <tbody>

    <?php foreach( $days as $day_key => $day_name ): ?>
        <?php $time_options = Inventor_Bookings_Logic::get_time_options_by_opening_hours( $field->object_id, $day_key ); ?>

        <tr>
            <td colspan="2" class="day">
                <strong><?php echo $day_name; ?></strong>
            </td>
        </tr>
                <?php if ( count( $time_options ) > 0 ): ?>
                    <?php $time_index = 0; ?>

                        <?php foreach( $time_options as $time ): ?>
                            <tr>
                                <td>
                                    <ul class="cmb2-list">
                                        <li>
                                            <?php $input_name = $field_type_object->_name( '['.$day_key.']['.$time_index.']' ); ?>
                                            <?php $input_name = $input_name.'[time]'; ?>
                                            <?php $input_id = $input_name.'['.$time.']'; ?>
                                            <?php $times = array_map( function( $time_config ) { return array_key_exists( 'time', $time_config ) ? $time_config['time'] : null; }, $value[ $day_key ] ); ?>
                                            <?php $is_checked = in_array( $time, $times ); ?>
                                            <input type="checkbox" class="cmb2-option" name="<?php echo $input_name; ?>" id="<?php echo $input_id ?>" value="<?php echo $time; ?>" <?php echo $is_checked ? 'checked="checked"' : ''; ?>>
                                            <label for="<?php echo $input_id; ?>"><?php echo $time; ?></label>

            <!--                                --><?php //echo $field_type_object->multicheck( array(
            ////                                    'class' => 'cmb_option',
            //                                    'name'  => $input_name,
            //                                    'id'    => $input_id,
            ////                                    'options'   => $days,
            //                                    'options'   => '',
            ////                                    'value' => isset( $value[ $day_key ] ) ? $value[ $day_key ] : null,
            //                                ) ); ?>

                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <?php $input_name = $field_type_object->_name( '['.$day_key.']['.$time_index.']' ); ?>
                                    <?php $input_name = $input_name.'[discount]'; ?>
                                    <?php $input_id = $input_name.'['.$time.']'; ?>
                                    <?php $input_value = ! empty( $value[ $day_key ][ $time_index ]['discount'] ) ? $value[ $day_key ][ $time_index ]['discount'] : null; ?>
                                    <input type="number" pattern="\d*" min="0" max="100" class="cmb2-option" name="<?php echo $input_name; ?>" id="<?php echo $input_id ?>" value="<?php echo $input_value; ?>" placeholder="<?php echo __( 'Discount', 'inventor-bookings' ); ?>" style="width:90px">
                                </td>
                            </tr>
                            <?php $time_index++; ?>
                        <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="2" class="closed">
                            <?php echo __( 'Closed', 'inventor-bookings' ); ?>
                        </td>
                    </tr>
                <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>
