from django.shortcuts import render
from django.http import HttpResponse
from django.template import loader

from .models import Place


def index(request):
    return render(request, 'places/index.html', {})

def wished(request):
    wished = Place.objects.filter(visited=False)
    return render(request, 'places/wished.html', {'wished': wished})

def visited(request):
    visited = Place.objects.filter(visited=True)
    return render(request, 'places/visited.html', {'visited': visited})
