#! /bin/bash
scriptDir=$( cd "$(dirname "${BASH_SOURCE[0]}")" ; pwd -P )
#echo AP9CPPCjVjus5vdNpVcYGN7NQpR | docker login -u ccsl_read --password-stdin https://docker-ccsl-virtual.repo.aes.alcatel.fr:8443


# If needed, add -v for verbose output.
DOCKER_REGISTRY_URL=docker-ccsl-virtual.repo.aes.alcatel.fr:8443/


docker build ${scriptDir} --tag=thalesmee \
    --build-arg DOCKER_REGISTRY_URL=${DOCKER_REGISTRY_URL} 
