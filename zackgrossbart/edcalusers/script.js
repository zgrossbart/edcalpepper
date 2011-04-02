if (!Mint.SI) { Mint.SI = new Object(); }
Mint.SI.WindowWidth = 
{
	onsave	: function() 
	{
        if (edcal) {
            
            /*
             * Collect the number of weeks they are showing
             */
            var val = '&edcal_weeks=' + edcal.weeksPref;
            
            
            /*
             * If they are showing authors
             */
            if (edcal.authorPref) {
                val += '&edcal_author=1';
            } else {
                val += '&edcal_author=0';
            }
            
            /*
             * Get the average number of posts they have
             * per day
             */
            var dayCounts = [];
            
            jQuery("#cal_cont .dayobj > .postlist").each(function() {
                /*
                 * Don't consider days with zero posts
                 */
                if (jQuery(this).children().length > 0) {
                    dayCounts.push(jQuery(this).children().length);
                }
            });
            
            var total = 0;
            for (var i = 0; i < dayCounts.length; i++) {
                total += dayCounts[i];
            }
            
            if (dayCounts.length > 0) {
                val += '&edcal_posts=' + Math.round(total / dayCounts.length);
            } else {
                val += '&edcal_posts=0';
            }
            
            return val;
                
        }
	}
};
