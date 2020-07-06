#!/bin/bash
for i in {1..10}
do
	../../ihtml -o temp.html template.html page.ccs
	rm temp.html
	echo "$i times"
done

