#!/bin/bash

SESSION_ID=$1
RESULT_ID=$2
PATH_CMD=.
VIDEO_DIR=storage/app/public/results/videos/
SESSION_DIR=storage/app/public/results/sessions/
PATH_SESSION="${SESSION_DIR}${SESSION_ID}"
PATH_WEBM="${VIDEO_DIR}${RESULT_ID}.webm"
PATH_MP4="${VIDEO_DIR}${RESULT_ID}.mp4"

echo "Session ID [${SESSION_ID}] video file merge start."
cat ${PATH_SESSION}/*.webm > ${PATH_WEBM}
echo "Session ID [${SESSION_ID}] video file merge ended."

echo "Result ID [${RESULT_ID}] Conversion start."
# ffmpeg -i <input> -filter:v fps=fps=60 <output>
echo "ffmpeg -i ${PATH_WEBM} -filter:v fps=fps=60 -y ${PATH_MP4}"
ffmpeg -i "${PATH_WEBM}" -filter:v fps=fps=60 -y "${PATH_MP4}"
echo "Result ID [${RESULT_ID}] Conversion ended."

${PATH_CMD}/pulse/pulse.py --id="$RESULT_ID" --video="${PATH_MP4}"
${PATH_CMD}/eyeris/eyeris.py --id="$RESULT_ID" --webm="${PATH_MP4}"
${PATH_CMD}/emotions/emotions.py --id="$RESULT_ID" --webm="${PATH_MP4}"
${PATH_CMD}/blinks/blinks.py --id="$RESULT_ID" --video="${PATH_MP4}"
#${PATH_CMD}/slouch/slouch.py --id="$RESULT_ID" --video="${PATH_MP4}"