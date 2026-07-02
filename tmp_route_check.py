import os
import re

controller_re = re.compile(r"['\"]([A-Za-z0-9_\\]+)@([A-Za-z0-9_]+)['\"]")
class_re = re.compile(r"\[\s*([^\]]+)::class\s*,")

routes_dir = os.path.join(os.getcwd(), 'routes')
for root, dirs, files in os.walk(routes_dir):
    for f in files:
        if f.endswith('.php'):
            path = os.path.join(root, f)
            text = open(path, 'r', encoding='utf-8', errors='ignore').read()
            for match in controller_re.finditer(text):
                controller = match.group(1)
                if '\\' in controller:
                    controller_file = os.path.join(os.getcwd(), 'app', 'Http', 'Controllers', *controller.split('\\')) + '.php'
                else:
                    controller_file = os.path.join(os.getcwd(), 'app', 'Http', 'Controllers', controller + '.php')
                if not os.path.exists(controller_file):
                    print(f'MISSING (string route): {controller} in {path}:{text[:match.start()].count("\n") + 1} -> {controller_file}')
            for match in class_re.finditer(text):
                class_ref = match.group(1).strip()
                if class_ref.endswith('Controller') and '::class' in match.group(0):
                    if class_ref.startswith('\\'):
                        class_parts = class_ref.lstrip('\\').split('\\')
                    else:
                        class_parts = class_ref.split('\\')
                    controller_file = os.path.join(os.getcwd(), 'app', 'Http', 'Controllers', *class_parts) + '.php'
                    if not os.path.exists(controller_file):
                        print(f'MISSING (class::class): {class_ref} in {path}:{text[:match.start()].count("\n") + 1} -> {controller_file}')
