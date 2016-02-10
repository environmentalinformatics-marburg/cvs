#!/bin/bash

pdflatex CV_TAppelhans.tex
biber CV_TAppelhans
biber CV_TAppelhans
pdflatex CV_TAppelhans.tex
pdflatex CV_TAppelhans.tex
