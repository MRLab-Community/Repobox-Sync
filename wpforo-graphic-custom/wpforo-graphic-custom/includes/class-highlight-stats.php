<?php
if (!defined('ABSPATH')) exit;

class WPGC_Highlight_Stats {
    
    public function __construct() {
        // Hook eseguito alla fine di ogni elemento forum nel loop
        add_action('wpforo_loop_hook', [$this, 'inject_stats_highlight'], 10, 2);
    }

    public function inject_stats_highlight($key, $forum) {
        // Inietta CSS e JS solo una volta (al primo passaggio del loop)
        if ($key === 0) {
            $this->load_assets();
        }
        
        // Output dello script di trasformazione
        ?>
        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const forumId = <?php echo (int)$forum['forumid']; ?>;
            const container = document.getElementById('wpf-forum-' + forumId);
            if (!container) return;

            // Seleziona i box statistiche specifici di questo forum
            const statBoxes = container.querySelectorAll('.wpf-stat-box');
            
            statBoxes.forEach((box, index) => {
                const labelDiv = box.querySelector('.wpf-sbl');
                const valueDiv = box.querySelector('.wpf-sbd');
                
                if (!labelDiv || !valueDiv) return;

                const labelText = labelDiv.textContent.trim().toLowerCase();
                
                // Mappatura etichette -> classi custom
                let targetClass = '';
                if (labelText.includes('discuss') || labelText.includes('topic')) {
                    targetClass = 'wpf-sbd-topic';
                } else if (labelText.includes('post') || labelText.includes('messagg')) {
                    targetClass = 'wpf-sbd-post';
                }

                if (targetClass) {
                    valueDiv.classList.add(targetClass);
                }
            });
        });
        </script>
        <?php
    }

    private function load_assets() {
        wp_enqueue_style(
            'wpgc-frontend', 
            WPGC_URL . 'assets/css/frontend.css', 
            [], 
            '1.0.0'
        );
    }
}