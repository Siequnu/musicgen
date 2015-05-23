musicgen is a smart soundtrack generator. It created a dynamic background track ideal for videos with voiceovers. 

Deployment notes:
This script needs read and write permissions to the /output and /content directories.
This script needs ffmpeg and timidity for media conversion (eg. $ brew install timidity). For web server use there are fallback static binaries included in the main folder, which the program will try to use if the system ffmpeg and ffprobe fail.

Usage indications:
Main entry to program: index.php. Submit the video ID of any YouTube video. Processing will take some time, depending on the length of the source video.

After successfully running the program, a .mp4 video will be written in the /output directory, combining the original video with the new soundtrack. The browser will attempt to play back the video. 

Known issues:
Monetised YouTube videos might attempt to show an ad at the start. In this case, the script will be unable to process the requested video. Try another video.