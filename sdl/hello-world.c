#include <stdio.h>
#include <SDL2/SDL.h>

#define FPS 60

int main(void)
{
    if (SDL_Init(SDL_INIT_VIDEO) < 0) {
        fprintf(stderr, "Init: %s\n", SDL_GetError());
        return 1;
    }

    SDL_Window *win = SDL_CreateWindow("hello, SDL2",
                                       SDL_WINDOWPOS_UNDEFINED, SDL_WINDOWPOS_UNDEFINED,
                                       800, 600,
                                       SDL_WINDOW_RESIZABLE);
    if (win == NULL) {
        fprintf(stderr, "CreateWindow: %s\n", SDL_GetError());
        return 1;
    }

    SDL_Renderer *rend = SDL_CreateRenderer(win, -1, SDL_RENDERER_ACCELERATED);
    if (rend == NULL) {
        fprintf(stderr, "CreateRenderer: %s\n", SDL_GetError());
        return 1;
    }

    Uint64 frame_delay = 1000.0f / FPS;
    Uint64 frame_last_start = SDL_GetTicks64();

    int quit = 0;
    while (quit == 0) {
        Uint64 frame_start = SDL_GetTicks64();
        float dt = (frame_start - frame_last_start) / 1000.0f;
        frame_last_start = frame_start;

        SDL_Event event;
        while (SDL_PollEvent(&event) == 1) {
            switch (event.type) {
            case SDL_QUIT:
                quit = 1;
                break;
            }
        }

        SDL_SetRenderDrawColor(rend, 0x1a, 0x1a, 0x1a, 0xff);
        SDL_RenderClear(rend);

        SDL_RenderPresent(rend);

        Uint64 frame_time = SDL_GetTicks64() - frame_start;
        if (frame_time < frame_delay) {
            SDL_Delay(frame_delay - frame_time);
        }
    }

    SDL_DestroyRenderer(rend);
    SDL_DestroyWindow(win);
    SDL_Quit();

    return 0;
}
