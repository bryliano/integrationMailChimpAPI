<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
	<?php wp_head(); ?>
</head>

<body>

<form id="subscribe">

    <label>
        <input type="text" name="name">
    </label>

    <label>
        <input type="email" name="email">
    </label>

    <label>
        <select name="position">

            <option value="CEO"><?php _e( 'CEO/Co-founder' ); ?></option>
            <option value="CTO"><?php _e( 'CTO/VP of Engineering' ); ?></option>
            <option value="PM"><?php _e( 'Project/Product Manager' ); ?></option>
            <option value="Engineer"><?php _e( 'Software Engineer' ); ?></option>
            <option value="Other"><?php _e( 'Other' ); ?></option>

        </select>
    </label>

    <button type="submit"><?php _e( 'Subscribe' ); ?></button>
    <input type="hidden" name="action" value="subscribe">

    <div class="answer"></div>

</form>

<?php wp_footer(); ?>

</body>

</html>