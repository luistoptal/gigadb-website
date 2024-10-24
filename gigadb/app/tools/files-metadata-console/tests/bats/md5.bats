#!/usr/bin/env bats

teardown () {
    echo "Executing teardown code"
    FILES="./102480.md5
    ./102480.filesizes"

    for file in $FILES
    do
      echo "Deleting $file"
      if [ -f "$file" ] ; then
          rm "$file"
      fi
    done
}

@test "No DOI provided" {
    cd ./tests/_data/102480
    run ../../../scripts/md5.sh
    [ "$status" -eq 1 ]
    [ "${lines[0]}" = "Error: DOI is required!" ]
    [ "${lines[1]}" = "Usage: ../../../scripts/md5.sh <DOI>" ]
    [ "${lines[2]}" = "Calculates and uploads MD5 checksums values and file sizes for the given DOI to the aws s3 bucket - gigadb-datasets-metadata." ]
}

@test "DOI provided" {
    cd ./tests/_data/102480
    run ../../../scripts/md5.sh 102480
    [ "${lines[0]}" = "Created 102480.md5" ]
    [ "${lines[1]}" = "Created 102480.filesizes" ]
    [ -f 102480.md5 ]
    [ -f 102480.filesizes ]
}

@test "confirm the md5 and the file size values " {
    cd ./tests/_data/102480
    run ../../../scripts/md5.sh 102480
    run grep './analysis_data/Tree_file.txt' ./102480.md5
    [ "$output" = '67d9336ca3b61384185dc665026a2325  ./analysis_data/Tree_file.txt' ]
    run grep './readme_102480.txt' ./102480.md5
    [ "$output" = '1b31864478eec0479ba76ff242cd7dbc  ./readme_102480.txt' ]
    run grep './analysis_data/Tree_file.txt' ./102480.filesizes
    [ "$output" = '     359 ./analysis_data/Tree_file.txt' ]
    run grep './readme_102480.txt' ./102480.filesizes
    [ "$output" = '    3202 ./readme_102480.txt' ]
}

