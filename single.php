<?php get_header(); ?>

<main>

    <?php if (have_posts()) : ?>

        <?php while (have_posts()) : the_post(); ?>

            <article>
                <h2><?php the_title(); ?></h2>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail( 'large' ); ?>
                    </div>
                <?php endif; ?>

                <p><?php the_content(); ?></p>
            </article>

        <?php endwhile; ?>

    <?php else : ?>

        <p>Geen posts gevonden.</p>

    <?php endif; ?>

</main>

<?php get_footer(); ?>