/*******************************************************************************
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 ******************************************************************************/

if (!Mint.SI) { Mint.SI = new Object(); }
Mint.SI.EdcalStats = 
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
            
            val += '&edcal_author_count=' + Mint.SI.EdcalStats.getAuthorCount();
            
            
            
            return val;
                
        }
	},
    
    getAuthorCount: function() {
        var authorCount = 0;
        var authors = [];
        
        /*
         * Get the number of different authors they have
         */
        
        for(var index in edcal.posts) {
            var day = edcal.posts[index];
            if (day) {
                for (var i = 0; i < day.length; i++) {
                    if (day[i] && !Mint.SI.EdcalStats.containsElement(day[i].author, authors)) {
                        authorCount++;
                        authors.push(day[i].author);
                    }
                }
            }
        };
        
        return authorCount;
    },
    
    containsElement: function(/*Object*/ elem, /*Array*/ array) {
        for (var i = 0; i < array.length; i++) {
            if (array[i] === elem) {
                return true;
            }
        }
        
        return false;
    }
};
